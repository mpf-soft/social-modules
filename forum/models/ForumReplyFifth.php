<?php
/**
 * Created by MPF Framework.
 * Date: 2015-04-02
 * Time: 14:33
 */

namespace mpf\modules\forum\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumReplySecond
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $thread_id
 * @property int $reply_id
 * @property string $content
 * @property string $time
 * @property int $edited
 * @property string $edit_time
 * @property int $edit_user_id
 * @property int $deleted
 * @property int $score
 * @property int $user_group_id
 * @property \mpf\modules\forum\models\ForumThread $thread
 * @property \app\models\User $author
 * @property \app\models\User $editor
 * @property \mpf\modules\forum\models\ForumUserGroup $userGroup
 * @property \mpf\modules\forum\models\ForumReplyForth $parent
 */
class ForumReplyFifth extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_replies_second";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
            'id' => 'Id',
            'user_id' => 'Author',
            'thread_id' => 'Thread',
            'reply_id' => 'Reply',
            'content' => 'Content',
            'time' => 'Time',
            'edited' => 'Edited',
            'edit_time' => 'Edit Time',
            'edit_user_id' => 'Editor',
            'deleted' => 'Deleted',
            'score' => 'Score',
            'user_group_id' => 'User Group'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return [
            'thread' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumThread', 'thread_id'],
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edit_user_id'],
            'userGroup' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumUserGroup', 'user_group_id'],
            'parent' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumReplyForth', 'reply_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return [
            ["id, user_id, thread_id, reply_id, content, time, edited, edit_time, edit_user_id, deleted, score, user_group_id", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "user_id", "thread_id", "reply_id", "content", "time", "edited", "edit_time", "edit_user_id", "deleted", "score", "user_group_id"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
