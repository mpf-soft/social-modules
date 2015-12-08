<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:45
 */

namespace mpf\modules\forum\models;

use app\models\PageTag;
use mpf\helpers\ArrayHelper;
use mpf\modules\forum\components\Config;
use mpf\modules\forum\components\ModelHelper;
use mpf\modules\forum\components\Translator;
use mpf\modules\forum\components\UserAccess;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\web\helpers\Html;
use mpf\web\Session;
use mpf\WebApp;
use mpf\widgets\form\fields\ForumTextarea;
use mpf\widgets\form\fields\Markdown;

/**
 * Class ForumThread
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $subcategory_id
 * @property int $category_id
 * @property int $section_id
 * @property string $title
 * @property string $content
 * @property string $keywords
 * @property int $score
 * @property int $replies
 * @property int $first_level_replies
 * @property int $views
 * @property string $create_time
 * @property string $edit_time
 * @property int $edit_user_id
 * @property int $sticky
 * @property int $order
 * @property int $closed
 * @property int $last_reply_id
 * @property int $last_reply_user_id
 * @property int $last_reply_level
 * @property int $deleted
 * @property string $deleted_time
 * @property int $deleted_user_id
 * @property string $last_reply_date
 * @property \mpf\modules\forum\models\ForumSubcategory $subcategory
 * @property \mpf\modules\forum\models\ForumCategory $category
 * @property \app\models\User $owner
 * @property \app\models\User $editor
 * @property \app\models\User $lastActiveUser
 * @property \app\models\User $deletedBy
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
            'category_id' => 'Category',
            'section_id' => 'Section',
            'title' => 'Title',
            'content' => 'Content',
            'keywords' => 'Keywords',
            'score' => 'Score',
            'replies' => 'Replies',
            'views' => 'Views',
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
    public static function getRelations() {
        return [
            'subcategory' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumSubcategory', 'subcategory_id'],
            'category' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumCategory', 'category_id'],
            'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edit_user_id'],
            'lastActiveUser' => [DbRelations::BELONGS_TO, '\app\models\User', 'last_reply_user_id'],
            'deletedBy' => [DbRelations::BELONGS_TO, '\app\models\User', 'deleted_user_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["title, content, keywords", "safe, required", "on" => "insert"],
            ["id, user_id, subcategory_id, category_id, section_id, title, content, keywords, score, replies, views, create_time, edit_time, edit_user_id, sticky, order, closed, last_reply_id, last_reply_user_id, last_reply_date", "safe", "on" => "search"]
        ];
    }

    /**
     * Used on search page
     * @param string $text
     * @param int $section
     * @param int[] $categories
     * @param int[] $subcategories
     * @param int[] $authors
     * @param int $page
     * @return array|static[]
     */
    public static function countAllByKeyWords($text, $section, $categories = null, $subcategories = null, $authors = null, $page = 1) {
        if (!trim($text)) {
            return [];
        }
        $words = explode(" ", $text);
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->with = ['lastActiveUser', 'owner', 'subcategory', 'category'];
        $condition->join = "LEFT JOIN `forum_thread_tags` ON forum_thread_tags.thread_id = `t`.id";
        $wordsList = [];
        foreach ($words as $k => $word) {
            if (!trim($word))
                continue;
            $condition->setParam(":word_$k", $word);
            $wordsList[] = ":word_$k";
        }
        $words = implode(", ", $wordsList);
        $condition->addCondition("`t`.`title` LIKE :searchConcat OR (`forum_thread_tags`.`word` IN ($words))");
        $condition->setParam(":searchConcat", "%$text%");
        $condition->compareColumn("section_id", $section);
        if ($categories) {
            $condition->compareColumn("category_id", $categories);
        }
        if ($subcategories) {
            $condition->compareColumn("subcategory_id", $subcategories);
        }
        if ($authors) {
            $condition->compareColumn("user_id", $authors);
        }
        return self::count($condition);
    }

    /**
     * Used on search page
     * @param string $text
     * @param int $section
     * @param int[] $categories
     * @param int[] $subcategories
     * @param int[] $authors
     * @param int $page
     * @return array|static[]
     */
    public static function findAllByKeyWords($text, $section, $categories = null, $subcategories = null, $authors = null, $page = 1) {
        if (!trim($text)) {
            return [];
        }
        $words = explode(" ", $text);
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->with = ['lastActiveUser', 'owner', 'subcategory', 'category'];
        $condition->join = "LEFT JOIN `forum_thread_tags` ON forum_thread_tags.thread_id = `t`.id";
        $wordsList = [];
        foreach ($words as $k => $word) {
            if (!trim($word))
                continue;
            $condition->setParam(":word_$k", $word);
            $wordsList[] = ":word_$k";
        }
        $words = implode(", ", $wordsList);
        $condition->addCondition("`t`.`title` LIKE :searchConcat OR (`forum_thread_tags`.`word` IN ($words))");
        $condition->setParam(":searchConcat", "%$text%");
        $condition->compareColumn("section_id", $section);
        if ($categories) {
            $condition->compareColumn("category_id", $categories);
        }
        if ($subcategories) {
            $condition->compareColumn("subcategory_id", $subcategories);
        }
        if ($authors) {
            $condition->compareColumn("user_id", $authors);
        }
        $condition->limit = Config::value('FORUM_THREADS_PER_PAGE');
        $condition->order = '`t`.`id` DESC';
        $condition->offset = ($page - 1) * Config::value('FORUM_THREADS_PER_PAGE');
        return self::findAll($condition);
    }

    /**
     * Find all threads for selected subcategory separated per page
     * @param $subcategory
     * @param int $page
     * @return static[]
     */
    public static function findAllForSubcategory($subcategory, $page = 1) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn("subcategory_id", $subcategory);
        $condition->compareColumn("deleted", 0);
        $condition->with = ['lastActiveUser', 'owner'];
        $condition->limit = Config::value('FORUM_THREADS_PER_PAGE');
        $condition->order = '`t`.`order` ASC, `t`.`id` DESC';
        $condition->offset = ($page - 1) * Config::value('FORUM_THREADS_PER_PAGE');
        return self::findAll($condition);
    }

    /**
     * @param $userId
     * @param $sectionId
     * @param int $page
     * @return static[]
     */
    public static function findAllByUser($userId, $sectionId, $page = 1) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn("user_id", $userId);
        $condition->compareColumn("section_id", $sectionId);
        $condition->compareColumn("deleted", 0);
        $condition->with = ['lastActiveUser', 'owner'];
        $condition->limit = Config::value('FORUM_THREADS_PER_PAGE');
        $condition->order = '`t`.`id` DESC';
        $condition->offset = ($page - 1) * Config::value('FORUM_THREADS_PER_PAGE');
        return self::findAll($condition);
    }

    /**
     * @param $section
     * @param int $limit
     * @param int $offset
     * @return static[]
     */
    public static function findRecent($section, $limit = 20, $offset = 0) {
        $condition = new ModelCondition(['model' => ForumSubcategory::className()]);
        $condition->with = ['category'];
        $condition->compareColumn("category.section_id", $section);
        $condition->compareColumn("deleted", 0);
        $condition->fields = ['t.id'];
        $ids = ArrayHelper::get()->transform(ForumSubcategory::findAll($condition), 'id');
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->with = ['category', 'subcategory', 'lastActiveUser', 'owner'];
        $condition->addInCondition('subcategory_id', $ids);
        $condition->order = "id DESC";
        $condition->limit = $limit;
        $condition->offset = $offset;
        return self::findAll($condition);
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "user_id", "subcategory_id", "category_id", "section_id", "title", "content", "keywords", "score", "replies", "views", "create_time", "edit_time", "edit_user_id", "sticky", "order", "closed", "last_reply_id", "last_reply_user_id", "last_reply_date"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * After first time save to be called to update category info
     * @param ForumSubcategory $subcategory
     * @return bool
     */
    public function publishNew(ForumSubcategory $subcategory = null) {
        ModelHelper::createSubscription("thread.replies.{$this->id}", "thread");
        $this->subscribe();
        $subcategory = $subcategory ?: ForumSubcategory::findByPk($this->subcategory_id);
        $subcategory->last_active_thread_id = $this->id;
        $subcategory->last_activity_time = date('Y-m-d H:i:s');
        $subcategory->last_active_user_id = $this->user_id;
        $subcategory->last_activity = 'create';
        $subcategory->number_of_threads = ForumThread::countByAttributes(['subcategory_id' => $subcategory->id, 'deleted' => 0]);
        return $subcategory->save();
    }

    public function getStatus() {
        if ($this->sticky) {
            return 'sticky';
        } elseif ($this->closed) {
            return 'closed';
        }
        return 'new';
    }

    /**
     * @var ForumUser2Section
     */
    protected $sectionUser;

    /**
     * @param $sectionId
     * @return ForumUser2Section
     */
    public function getSectionUser($sectionId) {
        if (!$this->sectionUser) {
            $this->sectionUser = ForumUser2Section::findByAttributes(['section_id' => $sectionId, 'user_id' => $this->user_id], ['with' => ['group', 'title']]);
        }
        return $this->sectionUser;
    }

    public function getContent() {
        if (Config::value("FORUM_TEXT_PARSER_CALLBACK") && is_callable(Config::value("FORUM_TEXT_PARSER_CALLBACK"))){
            $text = call_user_func(Config::value("FORUM_TEXT_PARSER_CALLBACK"), $this->content);
        } else {
            $text = Markdown::processText(htmlentities($this->content));
        }
        return  $text .
        Html::get()->scriptFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/highlight.pack.js') .
        Html::get()->cssFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/styles/github.css') .
        Html::get()->script('hljs.tabReplace = \'    \';hljs.initHighlightingOnLoad();');
    }

    /**
     * @var bool
     */
    protected $_canEdit;

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function canEdit($categoryId = null, $sectionId = null) {
        if (!is_null($this->_canEdit)) {
            return $this->_canEdit;
        }

        $categoryId = $categoryId ?: $this->subcategory->category_id;
        $sectionId = $sectionId ?: $this->subcategory->category->section_id;
        if (WebApp::get()->user()->isGuest()) { //added a fix for guests;
            return $this->_canEdit = false;
        }

        if ((($this->closed || $this->deleted) && !($moderator = UserAccess::get()->isCategoryModerator($categoryId, $sectionId))) || UserAccess::get()->isMuted($sectionId)) {
            return $this->_canEdit = false;
        }

        if ($this->user_id == WebApp::get()->user()->id) {
            return $this->_canEdit = true;
        }
        return $this->_canEdit = isset($moderator) ? $moderator : UserAccess::get()->isCategoryModerator($categoryId, $sectionId);
    }

    public $sessionViewsTempKey = "forum-threads-visited";

    public function updateViews() {
        if (Session::get()->exists($this->sessionViewsTempKey)) {
            $threads = Session::get()->value($this->sessionViewsTempKey);
        } else {
            $threads = [];
        }
        if (!isset($threads[$this->id])) {
            $this->views++;
            $this->save();
            $threads[$this->id] = true;
            Session::get()->set($this->sessionViewsTempKey, $threads);
        }
    }

    public function afterMove($oldSub, $threadURL) {
        if ($oldSub == $this->subcategory_id)
            return; // same sub;
        $subcategory = ForumSubcategory::findByPk($oldSub);
        $subcategory->number_of_threads = ForumThread::countByAttributes(['subcategory_id' => $subcategory->id]);
        $subcategory->save();
        $subcategory = ForumSubcategory::findByPk($this->subcategory_id);
        $subcategory->number_of_threads = ForumThread::countByAttributes(['subcategory_id' => $subcategory->id]);
        $subcategory->save();
        if (WebApp::get()->user()->id != $this->user_id) {
            ModelHelper::notifyUser('thread.moved', $threadURL, [
                "admin" => WebApp::get()->user()->name,
                "title" => $this->title,
                "newCategory" => $subcategory->category->name . Config::value('FORUM_PAGE_TITLE_SEPARATOR') . $subcategory->title
            ], $this->user_id);
        }
    }

    public function getAuthorIcon() {
        return Config::value('USER_ICON_FOLDER_URL') . ($this->owner->icon ?: 'default.png');
    }

    /**
     * @var bool|string
     */
    protected $_voted;

    /**
     * Get information about active user vote status for this thread;
     * @return bool|string
     */
    public function getMyVote() {
        if (WebApp::get()->user()->isGuest())
            return false;
        if (!is_null($this->_voted)) {
            return $this->_voted;
        }
        $vote = $this->_db->table('forum_thread_votes')->where("thread_id = :id AND user_id = :user")
            ->setParams([':id' => $this->id, ':user' => WebApp::get()->user()->id])->first();
        if (!$vote) {
            $this->_voted = false;
            return false;
        }
        return $this->_voted = ($vote['vote'] ? 'positive' : 'negative');
    }

    /**
     * Remembers if user is subscribed to current thread or not
     * @var bool
     */
    protected $_subscribed;

    public function ImSubscribed() {
        if (is_null($this->_subscribed)) {
            $exists = $this->_db->table('forum_users_subscriptions')->where("user_id = :user AND thread_id = :thread")
                ->setParams([':user' => WebApp::get()->user()->id, ':thread' => $this->id])
                ->first();
            $this->_subscribed = (bool)$exists;
        }
        return $this->_subscribed;
    }

    /**
     * Subscribe to current thread
     */
    public function subscribe() {
        $this->_db->table('forum_users_subscriptions')->insert([
            'user_id' => WebApp::get()->user()->id,
            'thread_id' => $this->id
        ], 'ignore');
        ModelHelper::subscribe("thread.replies.{$this->id}");
    }

    /**
     * Unsubscribe from current thread;
     */
    public function unsubscribe() {
        $this->_db->table('forum_users_subscriptions')->where("user_id = :user AND thread_id = :thread")
            ->setParams([':user' => WebApp::get()->user()->id, ':thread' => $this->id])
            ->delete();
        $this->_subscribed = false;
        ModelHelper::unsubscribe("thread.replies.{$this->id}");
    }

    public function newNotification($sectionId, $action = 'newReply') {
        $params = ['id' => $this->id, 'subcategory' => $this->subcategory->url_friendly_title, 'category' => $this->subcategory->category->url_friendly_name];
        if ($sectionId && 'get' == Config::value('FORUM_SECTION_ID_SOURCE')) {
            $params[Config::value('FORUM_SECTION_ID_KEY')] = $sectionId;
        }
        $url = ['thread', 'index', $params, WebApp::get()->request()->getModule()];
        $actions = [
            'newReply' => Translator::get()->translate('posted a new reply'),
            'editReply' => Translator::get()->translate('edited a reply'),
            'editThread' => Translator::get()->translate("edited the thread")
        ];
        ModelHelper::notifySubscribers("thread.replies.{$this->id}", $url, [
            'threadTitle' => $this->title,
            'action' => $actions[$action],
            'userName' => WebApp::get()->user()->name
        ]);

    }
}
