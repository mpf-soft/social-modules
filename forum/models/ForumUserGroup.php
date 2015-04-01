<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 12:54
 */

namespace mpf\modules\forum\models;

use app\components\htmltools\Messages;
use mpf\modules\forum\components\UserAccess;
use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumUserGroup
 * @package app\models
 * @property int $id
 * @property int $section_id
 * @property string $full_name
 * @property string $html_class
 * @property int $admin
 * @property int $moderator
 * @property int $newthread
 * @property int $threadreply
 * @property int $canread
 * @property \mpf\modules\forum\models\ForumSection $section
 */
class ForumUserGroup extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_user_groups";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
            'id' => 'Id',
            'section_id' => 'Section',
            'full_name' => 'Full Name',
            'html_class' => 'Html Class',
            'admin' => 'Admin',
            'moderator' => 'Moderator',
            'newthread' => 'Can create a new thread',
            'threadreply' => 'Can reply to open threads',
            'canread' => 'Can read the forum'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'section' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumSection', 'section_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, section_id, full_name, html_class, admin, moderator, newthread, threadreply, canread", "safe", "on" => "search"]
        ];
    }

    /**
     * @param $user
     * @param $section
     * @return static
     */
    public static function findByUserAndSection($user, $section){
        $condition = new ModelCondition();
        $condition->join = "INNER JOIN `forum_users2groups` ON group_id = `t`.`id` AND user_id = :user";
        $condition->compareColumn("section_id", $section);
        $condition->setParam(":user", $user);
        $condition->fields = "`t`.*";
        return self::find($condition);
    }

    /**
     * @param $section
     * @return static[]
     */
    public static function findAllBySection($section){
        return self::findAllByAttributes(['section_id' => $section]);
    }


    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @param int $sectionId
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider($sectionId) {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "full_name", "html_class", "admin", "moderator", "newthread", "threadreply", "canread"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        $condition->compareColumn("section_id", $sectionId);
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    public function beforeDelete(){
        if (is_a(App::get(), '\mpf\WebApp')) {
            if (!UserAccess::get()->isSectionAdmin($this->section_id)) {
                Messages::get()->error("You don't have access to delete this user group!");
                return false;
            }
        }
        return parent::beforeDelete();
    }

    public function beforeSave(){
        if (is_a(App::get(), '\mpf\WebApp')) {
            if (!UserAccess::get()->isSectionAdmin($this->section_id)) {
                Messages::get()->error("You don't have access to edit this user group!");
                return false;
            }
        }
        return parent::beforeSave();
    }
}
