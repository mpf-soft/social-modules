<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:43
 */

namespace app\modules\forum\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumSubcategory
 * @package app\models
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property int $last_thread_created_id
 * @property int $last_thread_updated_id
 * @property string $title
 * @property string $description
 * @property string $last_update_time
 * @property string $last_response_time
 * @property int $last_active_user_id
 * @property \app\modules\forum\models\ForumCategory $category
 * @property \app\models\User $owner
 */
class ForumSubcategory extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_subcategories";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
             'id' => 'Id',
             'category_id' => 'Category',
             'user_id' => 'Owner',
             'last_thread_created_id' => 'Newest Thread',
             'last_thread_updated_id' => 'Lastest Updated Thread',
             'title' => 'Title',
             'description' => 'Description',
             'last_update_time' => 'Last Update Time',
             'last_response_time' => 'Last Response Time',
             'last_active_user_id' => 'Last Active User'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return [
             'category' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumCategory', 'category_id'],
             'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return [
            ["id, category_id, user_id, last_thread_created_id, last_thread_updated_id, title, description, last_update_time, last_response_time, last_active_user_id", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "category_id", "user_id", "last_thread_created_id", "last_thread_updated_id", "title", "description", "last_update_time", "last_response_time", "last_active_user_id"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
