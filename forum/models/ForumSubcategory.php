<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:43
 */

namespace mpf\modules\forum\models;

use app\components\htmltools\Messages;
use mpf\modules\forum\components\Translator;
use mpf\modules\forum\components\UserAccess;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

/**
 * Class ForumSubcategory
 * @package app\models
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property int $last_active_thread_id
 * @property int $last_activity
 * @property string $title
 * @property string $description
 * @property string $last_activity_time
 * @property int $last_active_user_id
 * @property int $number_of_threads
 * @property int $number_of_replies
 * @property string $url_friendly_title
 * @property string $icon
 * @property \mpf\modules\forum\models\ForumCategory $category
 * @property \app\models\User $owner
 * @property \app\models\User $lastActiveUser
 * @property \mpf\modules\forum\models\ForumThread $lastActiveThread
 */
class ForumSubcategory extends DbModel {

    /**
     * Recors if subcategory is hidden for user or not
     * @var bool
     */
    public $hidden = false;

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
            'title' => 'Title',
            'description' => 'Description',
            'last_active_thread_id' => 'Newest Thread',
            'last_activity' => 'Last action',
            'last_activity_time' => 'Last Activity Time',
            'last_active_user_id' => 'Last Active User',
            'icon' => 'Icon',
            'number_of_threads' => 'Threads',
            'number_of_replies' => 'Replies',
            'url_friendly_title' => 'URL Friendly Title'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'category' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumCategory', 'category_id'],
            'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'lastActiveThread' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumThread', 'last_active_thread_id'],
            'lastActiveUser' => [DbRelations::BELONGS_TO, '\app\models\User', 'last_active_user_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, category_id, user_id, last_active_thread_id, last_activity, title, url_friendly_title, description, last_activity_time, last_active_user_id, number_of_threads, number_of_replies", "safe", "on" => "search"]
        ];
    }

    public static function getAllForSelectTree($sectionId){
        $categories = ForumCategory::findAllByAttributes(['section_id' => $sectionId]);
        $options = [];
        foreach ($categories as $cat){
            $options[$cat->name] = [];
            foreach ($cat->subcategories as $sub){
                $options[$cat->name][$sub->id] = $sub->title;
            }
        }
        return $options;
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @param int $category
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider($category = null) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        if ($category){
            $condition->compareColumn("category_id", $category);
        }
        foreach (["id", "category_id", "user_id", "last_active_thread_id", "last_activity", "title", "url_friendly_title", "description", "last_activity_time", "last_active_user_id", "number_of_threads", "number_of_replies"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    public function beforeSave() {
        if ($this->isNewRecord()) {
            $this->user_id = WebApp::get()->user()->id;
        }
        return parent::beforeSave();
    }

    public function beforeDelete() {
        if (!UserAccess::get()->isCategoryAdmin($this->category_id, $this->category->section_id)) {
            Messages::get()->error("You can't delete this category!");
            return false;
        }
        return parent::beforeDelete();
    }

    /**
     * @return ForumThread[]
     */
    public function getTopPostsForCategoryPage(){
        $condition = new ModelCondition(['model' => ForumThread::className()]);
        $condition->with = ['lastActiveUser', 'owner'];
        $condition->compareColumn("subcategory_id", $this->id);
        $condition->compareColumn('deleted', 0);
        $condition->order = "`t`.`order` DESC";
        $condition->limit = 10;
        return ForumThread::findAll($condition);
    }

    /**
     * @return string
     */
    public function getActionForList(){
        switch ($this->last_activity){
            case "create":
                return Translator::get()->translate("Created By");
            case "edit":
                return Translator::get()->translate("Edited By");
            case "reply":
                return Translator::get()->translate("Reply from");
        }
    }

}
