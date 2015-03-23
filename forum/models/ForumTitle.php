<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-23
 * Time: 12:55
 */

namespace app\modules\forum\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumTitle
 * @package app\models
 * @property int $id
 * @property int $section_id
 * @property string $title
 * @property string $icon
 * @property string $description
 */
class ForumTitle extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_titles";
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
             'title' => 'Title',
             'icon' => 'Icon',
             'description' => 'Description'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return [
             
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return [
            ["id, section_id, title, icon, description", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @param int $sectionId
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider($sectionId) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn('section_id', $sectionId);
        foreach (["id", "section_id", "title", "icon", "description"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
