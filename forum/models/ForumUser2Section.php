<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-23
 * Time: 12:50
 */

namespace app\modules\forum\models;

use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumUser2Section
 * @package app\modules\forum
 * @property int $id
 * @property int $user_id
 * @property int $section_id
 * @property int $muted
 * @property int $banned
 * @property int $title_id
 * @property int $group_id
 * @property string $member_since
 * @property string $signature
 * @property \app\models\User $user
 * @property \app\modules\forum\models\ForumSection $section
 * @property \app\modules\forum\models\ForumUserGroup $group
 * @property \app\modules\forum\models\ForumTitle $title
 */
class ForumUser2Section extends DbModel {

    public $name;
    public $last_login;

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_users2sections";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
            'id' => 'Id',
            'user_id' => 'User',
            'section_id' => 'Section',
            'muted' => 'Muted',
            'banned' => 'Banned',
            'title_id' => 'Title',
            'group_id' => 'Group',
            'member_since' => 'Member Since',
            'signature' => 'Signature'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'user' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'section' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumSection', 'section_id'],
            'group' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumUserGroup', 'group_id'],
            'title' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumTitle', 'title_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, user_id, section_id, muted, banned, title_id, group_id, member_since, signature", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider($sectionId) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->with = ['user'];
        $condition->compareColumn("section_id", $sectionId);
        foreach (["id", "user_id", "section_id", "muted", "banned", "title_id", "group_id", "member_since", "signature"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * Makes a new user member of a selected section with a selected group.
     * @param $userId
     * @param $sectionId
     * @param $groupId
     * @return bool
     */
    public static function makeMember($userId, $sectionId = 0, $groupId = null){
        $user = new self();
        $user->user_id = $userId;
        $user->section_id = $sectionId;
        if (!$groupId){
            $section  =ForumSection::findByPk($sectionId);
            $groupId = $section->default_members_group_id;
        }
        App::get()->debug("User $userId assign to group $groupId from section $sectionId");
        $user->group_id = $groupId;
        return $user->save();
    }
}
