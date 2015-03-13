<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:45
 */

namespace app\modules\forum\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumThread
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $subcategory_id
 * @property string $title
 * @property string $content
 * @property int $score
 * @property int $replies
 * @property string $create_time
 * @property string $edit_time
 * @property int $edit_user_id
 * @property int $sticky
 * @property int $order
 * @property int $closed
 * @property int $last_reply_id
 * @property int $last_reply_user_id
 * @property string $last_reply_date
 * @property \app\modules\forum\models\ForumSubcategory $subcategory
 * @property \app\models\User $owner
 * @property \app\models\User $editor
 * @property \app\models\User $lastActiveUser
 */
class ForumThread extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_threads";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
             'id' => 'Id',
             'user_id' => 'Owner',
             'subcategory_id' => 'Subcategory',
             'title' => 'Title',
             'content' => 'Content',
             'score' => 'Score',
             'replies' => 'Replies',
             'create_time' => 'Create Time',
             'edit_time' => 'Edit Time',
             'edit_user_id' => 'Updated by',
             'sticky' => 'Sticky',
             'order' => 'Order',
             'closed' => 'Closed',
             'last_reply_id' => 'Last Reply',
             'last_reply_user_id' => 'Last User That Replied',
             'last_reply_date' => 'Last Reply Date'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return [
             'subcategory' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumSubcategory', 'subcategory_id'],
             'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
             'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edit_user_id'],
             'lastActiveUser' => [DbRelations::BELONGS_TO, '\app\models\User', 'last_reply_user_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return [
            ["id, user_id, subcategory_id, title, content, score, replies, create_time, edit_time, edit_user_id, sticky, order, closed, last_reply_id, last_reply_user_id, last_reply_date", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "user_id", "subcategory_id", "title", "content", "score", "replies", "create_time", "edit_time", "edit_user_id", "sticky", "order", "closed", "last_reply_id", "last_reply_user_id", "last_reply_date"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
