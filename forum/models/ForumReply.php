<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:49
 */

namespace app\modules\forum\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class ForumReply
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $thread_id
 * @property string $content
 * @property string $time
 * @property int $edited
 * @property string $edit_time
 * @property int $edit_user_id
 * @property int $deleted
 * @property int $score
 * @property int $user_group_id
 * @property \app\models\User $author
 * @property \app\modules\forum\models\ForumThread $thread
 * @property \app\models\User $editor
 * @property \app\modules\forum\models\ForumUserGroup $authorGroup
 */
class ForumReply extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_replies";
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
             'content' => 'Content',
             'time' => 'Time',
             'edited' => 'Edited',
             'edit_time' => 'Edit Time',
             'edit_user_id' => 'Editor',
             'deleted' => 'Deleted',
             'score' => 'Score',
             'user_group_id' => 'Author Group'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return [
             'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
             'thread' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumThread', 'thread_id'],
             'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edit_user_id'],
             'authorGroup' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumUserGroup', 'user_group_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return [
            ["id, user_id, thread_id, content, time, edited, edit_time, edit_user_id, deleted, score, user_group_id", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "user_id", "thread_id", "content", "time", "edited", "edit_time", "edit_user_id", "deleted", "score", "user_group_id"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }
}
