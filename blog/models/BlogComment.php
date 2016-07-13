<?php
/**
 * Created by MPF Framework.
 * Date: 2016-07-07
 * Time: 08:48
 */

namespace mpf\modules\blog\models;

use app\models\User;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

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
 * @property string $email
 * @property string $text
 * @property string $published_time
 * @property string $last_edit_time
 * @property User $author
 */
class BlogComment extends DbModel
{
    const STATUS_OK = 1;
    const STATUS_DELETED = 2;

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
            'username' => 'Name',
            'email' => 'Email',
            'text' => 'Comment',
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
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["text", "required", "on" => "insert"],
            ["username, text, email", "safe", "on" => "insert, edit"],
            ['email', 'email'],
            ["id, post_id, user_id, edits, status, edited_by, username, text, published_time, last_edit_time, email", "safe", "on" => "search"]
        ];
    }

    public function beforeSave()
    {
        if (WebApp::get()->user()->isConnected()) {
            $this->user_id = WebApp::get()->user()->id;
            $this->username = WebApp::get()->user()->name;
            $this->email = WebApp::get()->user()->email;
        } elseif (!trim($this->email)) {
            $this->setError('email', 'Email is required  for guests!');
            return false;
        } elseif (!trim($this->username)) {
            $this->setError('username', 'Name is required for quests!');
            return false;
        }
        $this->published_time = date('Y-m-d H:i:s');
        $this->status = BlogComment::STATUS_OK;
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "post_id", "user_id", "edits", "status", "edited_by", "username", "text", "published_time", "email", "last_edit_time"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    public function getAuthorIcon(){

    }
}
