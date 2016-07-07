<?php
/**
 * Created by MPF Framework.
 * Date: 2016-07-07
 * Time: 08:48
 */

namespace mpf\modules\blog\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class BlogComment
 * @package app\models
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int $edits
 * @property int $status
 * @property int $edited_by
 * @property string $username
 * @property string $text
 * @property string $published_time
 * @property string $last_edit_time
 */
class BlogComment extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "blog_comments";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels()
    {
        return [
            'id' => 'Id',
            'post_id' => 'Post',
            'user_id' => 'User',
            'edits' => 'Edits',
            'status' => 'Status',
            'edited_by' => 'Edited By',
            'username' => 'Username',
            'text' => 'Text',
            'published_time' => 'Published Time',
            'last_edit_time' => 'Last Edit Time'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [

        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, post_id, user_id, edits, status, edited_by, username, text, published_time, last_edit_time", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "post_id", "user_id", "edits", "status", "edited_by", "username", "text", "published_time", "last_edit_time"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
