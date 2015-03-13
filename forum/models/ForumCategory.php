<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 12:55
 */

namespace app\modules\forum\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumCategory
 * @package app\models
 * @property int $id
 * @property string $name
 * @property string $url_friendly_name
 * @property int $order
 * @property int $user_id
 * @property int $section_id
 * @property \app\modules\forum\models\ForumSection $section
 * @property \app\models\User $owner
 */
class ForumCategory extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_categories";
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
             'url_friendly_name' => 'Url Friendly Name',
             'order' => 'Order',
             'user_id' => 'Owner',
             'section_id' => 'Section'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return [
             'section' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumSection', 'section_id'],
             'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return [
            ["id, name, url_friendly_name, order, user_id, section_id", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "name", "url_friendly_name", "order", "user_id", "section_id"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
