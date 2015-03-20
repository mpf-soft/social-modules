<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 12:53
 */

namespace app\modules\forum\models;

use app\components\htmltools\Messages;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumSection
 * @package app\models
 * @property int $id
 * @property string $name
 * @property int $default_user_group_id
 * @property int $owner_user_id
 * @property \app\models\User $owner
 * @property \app\modules\forum\models\ForumUserGroup $defaultGroup
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
            'default_user_group_id' => 'Default User Group',
            'owner_user_id' => 'Owner'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'owner_user_id'],
            'defaultGroup' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumUserGroup', 'default_user_group_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, name, default_user_group_id, owner_user_id", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "name", "default_user_group_id", "owner_user_id"] as $column) {
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
     * @param $groupId
     * @return bool
     */
    public function setDefaultGroup($groupId){
        $group = ForumUserGroup::findByPk($groupId);
        if (!$group){
            Messages::get()->error("Group not found!");
            return false;
        }
        if ($group->section_id != $this->id){
            Messages::get()->error("Group is assigned to a different section of the forum!");
            return false;
        }
        $this->default_user_group_id = $groupId;
        return $this->save();
    }
}
