<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:45
 */

namespace mpf\modules\forum\models;

use mpf\datasources\sql\DbRelation;
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
class ForumThread extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "forum_threads";
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
    public static function getRelations()
    {
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
    public static function getRules()
    {
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
    public static function countAllByKeyWords($text, $section, $categories = null, $subcategories = null, $authors = null, $page = 1)
    {
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
    public static function findAllByKeyWords($text, $section, $categories = null, $subcategories = null, $authors = null, $page = 1)
    {
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
    public static function findAllForSubcategory($subcategory, $page = 1)
    {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn("subcategory_id", $subcategory);
        $condition->compareColumn("deleted", 0);
        $condition->with = ['lastActiveUser', 'owner'];
        $condition->limit = Config::value('FORUM_THREADS_PER_PAGE');
        $condition->order = '`t`.`sticky` DESC, `t`.`order` ASC, `t`.`id` DESC';
        $condition->offset = ($page - 1) * Config::value('FORUM_THREADS_PER_PAGE');
        return self::findAll($condition);
    }

    /**
     * @param $userId
     * @param $sectionId
     * @param int $page
     * @return static[]
     */
    public static function findAllByUser($userId, $sectionId, $page = 1)
    {
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
    public static function findRecent($section, $limit = 20, $offset = 0)
    {
        return self::_findRecent([ForumUser2Section::findByAttributes(['user_id' => WebApp::get()->user()->id, 'section_id' => $section])], $limit, $offset, $section);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return ForumThread[]
     */
    public static function findRecentForActiveUser($offset = 0, $limit = 20)
    {
        if (WebApp::get()->user()->isConnected() && ($sections = ForumUser2Section::findAllByAttributes(['user_id' => WebApp::get()->user()->id, 'banned' => 0]))) {
            return self::_findRecent($sections, $limit, $offset);
        } elseif (WebApp::get()->user()->isGuest()) {
            return self::_findRecent([null], $limit, $offset, 0);
        }
        return [];
    }

    /**
     * @param ForumUser2Section[] $sections
     * @param $limit
     * @param $offset
     * @param null $sectionId
     * @return array|static[]
     */
    protected static function _findRecent($sections, $limit, $offset, $sectionId = null)
    {
        $guest = false;
        $sectionsIds = [];
        if (1 === count($sections) && is_null($sections[0])) {
            if (is_null($sectionId))
                return [];
            $sectionsIds = [$sectionId];
            $guest = true;
        } elseif (count($sections)) {
            $sectionsIds = ArrayHelper::get()->transform($sections, 'section_id');
        }
        $userId = WebApp::get()->user()->isConnected() ? WebApp::get()->user()->id : 0;
        $reload = !WebApp::get()->cache()->exists('User:' . $userId . ':visibleSubcategories');
        $ids = [];
        if (!$reload) {
            $info = WebApp::get()->cache()->value('User:' . $userId . ':visibleSubcategories');
            if ($info['time'] < (time() - 720)) { //force refresh once every 30m
                $reload = true;
            } else {
                $ids = $info['categories'];
            }
        }
        if ($reload) {
            $condition = new ModelCondition(['model' => ForumSubcategory::className()]);
            $condition->with = ['category'];
            if ($guest) {
                $groups = [ForumSection::findByPk($sectionId)->default_visitors_group_id];
            } else {
                $groups = ArrayHelper::get()->transform($sections, 'group_id');
            }
            if ($groups) {
                $groups = implode(', ', $groups);
                $condition->join = "LEFT JOIN forum_groups2categories ON (forum_groups2categories.category_id = category.id AND forum_groups2categories.group_id IN ($groups))";
                $condition->addCondition("forum_groups2categories.canread IS NULL OR forum_groups2categories.canread = 1");
                $condition->addInCondition('category.section_id', $sectionsIds);
                $categories = ForumSubcategory::findAll($condition);
            } else {
                $categories = [];
            }
            $ids = [];
            foreach ($categories as $cat) {
                if (!isset($ids[$cat->category->section_id])) {
                    $ids[$cat->category->section_id] = [];
                }
                $ids[$cat->category->section_id][] = $cat->id;
            }
            WebApp::get()->cache()->set('User:' . $userId . ':visibleSubcategories', [
                'time' => time(),
                'categories' => $ids
            ]);
        }
        $finalIDs = [];
        if ($guest) {
            $finalIDs = $ids[$sectionId];
        } else {
            foreach ($sections as $s) {
                if (isset($ids[$s->section_id])) {
                    foreach ($ids[$s->section_id] as $id)
                        $finalIDs[] = $id;
                }
            }
        }
        if (!$finalIDs) {
            return [];
        }

        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->with = ['category', 'subcategory', 'lastActiveUser', 'owner'];
        $condition->addInCondition('subcategory_id', $finalIDs);
        $condition->compareColumn('deleted', 0);
        $condition->order = "GREATEST(IFNULL(t.edit_time, t.create_time), IFNULL(t.last_reply_date, t.create_time)) DESC";
        $condition->limit = $limit;
        $condition->offset = $offset;
        return self::findAll($condition);
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
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
     * @param int $time
     * @return bool
     */
    public function publishNew(ForumSubcategory $subcategory = null, $time = null)
    {
        ModelHelper::createSubscription("thread.replies.{$this->id}", "thread");
        if ($this->user_id)
            $this->subscribe($this->user_id);
        $subcategory = $subcategory ?: ForumSubcategory::findByPk($this->subcategory_id);
        $subcategory->last_active_thread_id = $this->id;
        $subcategory->last_activity_time = date('Y-m-d H:i:s', $time ?: time());
        $subcategory->last_active_user_id = $this->user_id;
        $subcategory->last_activity = 'create';
        return $subcategory->recalculateNumbers()->save();
    }

    public function getStatus()
    {
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
    public function getSectionUser($sectionId)
    {
        if (!$this->sectionUser) {
            $this->sectionUser = ForumUser2Section::findByAttributes(['section_id' => $sectionId, 'user_id' => $this->user_id], ['with' => ['group', 'title']]);
            if (!$this->sectionUser) {
                ForumUser2Section::makeVisitor($this->user_id, $sectionId);
            }
        }
        return $this->sectionUser;
    }

    public function getContent()
    {
        if (Config::value("FORUM_TEXT_PARSER_CALLBACK") && is_callable(Config::value("FORUM_TEXT_PARSER_CALLBACK"))) {
            $text = call_user_func(Config::value("FORUM_TEXT_PARSER_CALLBACK"), $this->content);
        } else {
            $text = Markdown::processText($this->content);
        }
        return $text .
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
    public function canEdit($categoryId = null, $sectionId = null)
    {
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

    public function updateViews()
    {
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

    public function afterMove($oldSub, $threadURL)
    {
        if ($oldSub == $this->subcategory_id)
            return; // same sub;
        $sub = ForumSubcategory::findByPk($oldSub);
        if ($sub->last_active_thread_id = $this->id) {
            $sub->checkLastActivity();
        }
        $sub->recalculateNumbers()->save();
        ForumSubcategory::findByPk($this->subcategory_id)->checkLastActivity()->recalculateNumbers()->save();
        if (WebApp::get()->user()->id != $this->user_id) {
            ModelHelper::notifyUser('thread.moved', $threadURL, [
                "admin" => WebApp::get()->user()->name,
                'adminId' => WebApp::get()->user()->id,
                "title" => $this->title,
                "threadId" => $this->id,
                "newCategory" => $this->subcategory->category->name . Config::value('FORUM_PAGE_TITLE_SEPARATOR') . $this->subcategory->title
            ], $this->user_id);
        }
    }

    /**
     * @return string
     */
    public function getAuthorIcon()
    {
        return ModelHelper::getUserIconURL($this->owner->icon ?: 'default.png');
    }

    /**
     * @var bool|string
     */
    protected $_voted;

    /**
     * Get information about active user vote status for this thread;
     * @return bool|string
     */
    public function getMyVote()
    {
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

    public function ImSubscribed()
    {
        if (WebApp::get()->user()->isGuest())
            return false;
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
    public function subscribe($userId = null)
    {
        if (is_null($userId) && WebApp::get()->user()->isGuest())
            return false;
        if (0 === $userId) // for deleted users;
            return false;
        $this->_db->table('forum_users_subscriptions')->insert([
            'user_id' => $userId ?: WebApp::get()->user()->id,
            'thread_id' => $this->id
        ], 'ignore');
        ModelHelper::subscribe("thread.replies.{$this->id}", $userId);
    }

    /**
     * Unsubscribe from current thread;
     */
    public function unsubscribe()
    {
        if (WebApp::get()->user()->isGuest())
            return false;
        $this->_db->table('forum_users_subscriptions')->where("user_id = :user AND thread_id = :thread")
            ->setParams([':user' => WebApp::get()->user()->id, ':thread' => $this->id])
            ->delete();
        $this->_subscribed = false;
        ModelHelper::unsubscribe("thread.replies.{$this->id}");
    }

    public function newNotification($sectionId, $action = 'newReply')
    {
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
            'threadId' => $this->id,
            'userName' => WebApp::get()->user()->name,
            'userId' => WebApp::get()->user()->id
        ]);

    }

    /**
     * Moved user profile to config;
     * @param array $htmlOptions
     * @return string
     */
    public function getOwnerProfileLink($htmlOptions = [])
    {
        return Config::get()->getProfileLink($this->user_id, $this->owner->name, $htmlOptions);
    }

    /**
     * Moved user profile to config;
     * @param array $htmlOptions
     * @return string
     */
    public function getEditorProfileLink($htmlOptions = [])
    {
        return Config::get()->getProfileLink($this->edit_user_id, $this->editor->name, $htmlOptions);
    }

    /**
     * Moved user profile to config;
     * @param array $htmlOptions
     * @return string
     */
    public function getDeletedByProfileLink($htmlOptions = [])
    {
        return Config::get()->getProfileLink($this->deleted_user_id, $this->deletedBy->name, $htmlOptions);
    }

    /**
     * Moved user profile to config;
     * @param array $htmlOptions
     * @return string
     */
    public function getLastActiveProfileLink($htmlOptions = [])
    {
        return Config::get()->getProfileLink($this->last_reply_user_id, $this->lastActiveUser->name, $htmlOptions);
    }

    /**
     * @return array
     */
    public function getLink($module = null)
    {
        $s = $this->section_id;
        $r = ['thread', 'index', ['subcategory' => $this->subcategory->url_friendly_title, 'category' => $this->subcategory->category->url_friendly_name, 'id' => $this->id], $module];
        if (!$s)
            return $r;
        if ('get' != Config::value('FORUM_SECTION_ID_SOURCE'))
            return $r;
        if (isset($r[2]) && is_array($r[2])) {
            $r[2][Config::value('FORUM_SECTION_ID_KEY')] = $s;
        } elseif (isset($r[2])) {
            $r[3] = $r[2];
            $r[2] = [Config::value('FORUM_SECTION_ID_KEY') => $s];
        } else {
            $r[2] = [Config::value('FORUM_SECTION_ID_KEY') => $s];
        }

        return $r;
    }
}
