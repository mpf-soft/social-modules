<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 12:53
 */

namespace mpf\modules\forum\models;

use app\components\htmltools\Messages;
use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumSection
 * @package app\models
 * @property int $id
 * @property string $name
 * @property int $default_visitors_group_id
 * @property int $owner_user_id
 * @property int $default_members_group_id
 * @property \app\models\User $owner
 * @property \mpf\modules\forum\models\ForumUserGroup $defaultVisitorsGroup
 * @property \mpf\modules\forum\models\ForumUserGroup $defaultMembersGroup
 */
class ForumSection extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_sections";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'default_visitors_group_id' => 'Default Visitor Group',
            'owner_user_id' => 'Owner',
            'default_members_group_id' => 'Default Member Group'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'owner_user_id'],
            'defaultVisitorsGroup' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumUserGroup', 'default_visitors_group_id'],
            'defaultMembersGroup' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumUserGroup', 'default_members_group_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, name, default_visitors_group_id, default_members_group_id, owner_user_id", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "name", "default_visitors_group_id", "default_members_group_id", "owner_user_id"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * Set a different default group for current section. It will check if group exists and if it's assigned to this
     * section but it will not check if user has access to this section as this method will also be used by automated
     * processes when a new section is generated.
     * @param int $groupId
     * @param string $for
     * @return bool
     */
    public function setDefaultGroup($groupId, $for = 'visitor'){
        $group = ForumUserGroup::findByPk($groupId);
        if (!$group){
            Messages::get()->error("Group not found!");
            return false;
        }
        if ($group->section_id != $this->id){
            Messages::get()->error("Group is assigned to a different section of the forum!");
            return false;
        }
        if ('visitor' == $for) {
            $this->default_visitors_group_id = $groupId;
        } else {
            $this->default_members_group_id = $groupId;
        }
        return $this->save();
    }

    /**
     * Creates a new section + default user groups + a single user title. Use "Main" name if you only have one.
     * @param string $name
     * @param int $userId
     * @return int
     */
    public static function createNew($name = "Main", $userId){
        $section  = new self;
        $section->name = $name;
        $section->owner_user_id = $userId;
        if (!$section->save()) {
            return false;
        }
        if ("Main" == $name){
            App::get()->debug("Main section detected. Setting ID to 0!");
            $section->id = 0;
            $section->save();
        }
        App::get()->debug("Section $name:  #{$section->id} created!");
        $group = new ForumUserGroup();
        $group->section_id = $section->id;
        $group->full_name = 'Visitors';
        $group->html_class = 'visitors';
        $group->admin = $group->moderator = $group->newthread = $group->threadreply = 0;
        $group->canread = 1;
        $group->save();
        App::get()->debug("Group {$group->full_name}:  #{$group->id} created!");
        $section->default_visitors_group_id = $group->id;
        $group->full_name = "Members";
        $group->html_class = "members";
        $group->newthread = $group->threadreply = 1;
        $group->saveAsNew();
        App::get()->debug("Group {$group->full_name}:  #{$group->id} created!");
        $section->default_members_group_id = $group->id;
        $section->save();
        App::get()->debug("Section updated with default group ids!");
        $group->full_name = "Moderators";
        $group->html_class = "moderators";
        $group->moderator = 1;
        $group->saveAsNew();
        App::get()->debug("Group {$group->full_name}:  #{$group->id} created!");
        $group->full_name = "Admins";
        $group->html_class = "admins";
        $group->admin = 1;
        $group->saveAsNew();
        App::get()->debug("Group {$group->full_name}:  #{$group->id} created!");
        $title = new ForumTitle();
        $title->section_id = $section->id;
        $title->title = "New Comer";
        $title->icon = "default.png";
        $title->description = $title->title;
        $title->save();
        App::get()->debug("Title {$title->title}:  #{$title->id} created!");
        $user = new ForumUser2Section();
        $user->user_id = $userId;
        $user->section_id = $section->id;
        $user->group_id = $group->id;
        $user->title_id = $title->id;
        $user->banned = $user->muted = 0;
        $user->signature = '';
        $user->save();
        App::get()->debug("User #$userId assigned to section as admin! (Group: #{$group->id})");
        return $section->id;
    }

    /**
     * This deletes all posts, users, sections, everything. To be used once when app goes from dev to production or when it is installed.
     */
    public static function resetForum(){
        self::getDb()->execQuery("TRUNCATE TABLE `forum_categories`;
TRUNCATE TABLE `forum_groups2categories`;
TRUNCATE TABLE `forum_replies`;
TRUNCATE TABLE `forum_sections`;
TRUNCATE TABLE `forum_subcategories`;
TRUNCATE TABLE `forum_threads`;
TRUNCATE TABLE `forum_titles`;
TRUNCATE TABLE `forum_users2sections`;
TRUNCATE TABLE `forum_user_groups`;");
    }

}
