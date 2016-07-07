<?php
/**
 * Created by MPF Framework.
 * Date: 2016-07-07
 * Time: 08:41
 */

namespace mpf\modules\blog\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class BlogPost
 * @package app\models
 * @property int $id
 * @property int $author_id
 * @property int $category_id
 * @property string $time_written
 * @property string $time_published
 * @property int $status
 * @property int $edited_by
 * @property string $edit_time
 * @property int $edit_number
 * @property string $url
 * @property string $image_icon
 * @property string $image_cover
 * @property int $allow_comments
 * @property \app\models\User $author
 * @property \mpf\modules\blog\models\BlogCategory $category
 * @property \app\models\User $editor
 * @property \mpf\modules\blog\models\BlogComment[] $comments
 */
class BlogPost extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "blog_posts";
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
            'author_id' => 'Author',
            'category_id' => 'Category',
            'time_written' => 'Time Written',
            'time_published' => 'Time Published',
            'status' => 'Status',
            'edited_by' => 'Edited By',
            'edit_time' => 'Edit Time',
            'edit_number' => 'Edit Number',
            'url' => 'URL',
            'image_icon' => 'Image Icon',
            'image_cover' => 'Image Cover',
            'allow_comments' => 'Allow Comments'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'author_id'],
            'category' => [DbRelations::BELONGS_TO, '\mpf\modules\blog\models\BlogCategory', 'category_id'],
            'comments' => [DbRelations::HAS_MANY, BlogComment::className(), 'post_id'],
            'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edited_by']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, author_id, category_id, time_written, time_published, status, edited_by, edit_time, edit_number, url, image_icon, image_cover, allow_comments", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "author_id", "category_id", "time_written", "time_published", "status", "edited_by", "edit_time", "edit_number", "url", "image_icon", "image_cover", "allow_comments"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
