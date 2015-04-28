<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 13:49
 */

namespace mpf\modules\forum\models;

use app\models\PageTag;
use mpf\datasources\sql\DbRelation;
use mpf\modules\forum\components\Config;
use mpf\modules\forum\components\Translator;
use mpf\modules\forum\components\UserAccess;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\fields\ForumTextarea;

/**
 * Class ForumReply
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $thread_id
 * @property int $section_id
 * @property string $content
 * @property string $time
 * @property int $edited
 * @property string $edit_time
 * @property int $edit_user_id
 * @property int $deleted
 * @property string $deleted_time
 * @property int $deleted_user_id
 * @property int $score
 * @property int $user_group_id
 * @property \app\models\User $author
 * @property \mpf\modules\forum\models\ForumThread $thread
 * @property \app\models\User $editor
 * @property \mpf\modules\forum\models\ForumUserGroup $authorGroup
 * @property \mpf\modules\forum\models\ForumReplySecond[] $replies
 * @property \mpf\modules\forum\models\ForumUser2Section $sectionAuthor
 * @property \app\models\User $deletedBy
 * @property \mpf\modules\forum\models\ForumReplyVote $myVote
 */
class ForumReply extends DbModel {

    public static $currentSection = 0;

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
            'section_id' => 'Section',
            'content' => 'Reply',
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
    public static function getRelations() {
        return [
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'thread' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumThread', 'thread_id'],
            'sectionAuthor' => DbRelation::belongsTo(ForumUser2Section::className(), 'user_id')->hasAttributeValue('section_id', 'currentSection'),
            'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edit_user_id'],
            'deletedBy' => [DbRelations::BELONGS_TO, '\app\models\User', 'deleted_user_id'],
            'authorGroup' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumUserGroup', 'user_group_id'],
            'replies' => [DbRelations::HAS_MANY, '\mpf\modules\forum\models\ForumReplySecond', 'reply_id'],
            'myVote' => DbRelation::hasOne(ForumReplyVote::className())->columnsEqual('id', 'reply_id')->hasValue('level', 1)->hasValue('user_id', WebApp::get()->user()->isConnected()?WebApp::get()->user()->id:0)
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ['content', 'safe, required', 'on' => 'insert, edit'],
            ["id, user_id, thread_id, content, section_id, time, edited, edit_time, edit_user_id, deleted, score, user_group_id, reply_id", "safe", "on" => "search"]
        ];
    }


    public static function findAllRepliesForThread($id, $page = 1, $perPage = 20) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $with = [];
        for ($i = 0; $i <= Config::value('FORUM_MAX_REPLY_LEVELS'); $i++){
            foreach (['author', 'editor', 'authorGroup', 'sectionAuthor', 'sectionAuthor.title', 'replies', 'myVote'] as $child) {
                $with[] = str_repeat('replies.', $i) . $child;
            }
        }
        $condition->with = $with;
        $condition->compareColumn("thread_id", $id);
        $condition->limit = $perPage;
        $condition->order = '`t`.`id` ASC';
        $condition->offset = ($page - 1) * $perPage;
        return self::findAll($condition);

    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "user_id", "reply_id", "section_id", "thread_id", "content", "time", "edited", "edit_time", "edit_user_id", "deleted", "score", "user_group_id"] as $column) {
            if ($column == "reply_id" && $this->_tableName == 'forum_replies')
                continue;
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    public function getContent() {
        if ($this->deleted) {
            if ($this->deleted_user_id != $this->user_id){
                return Html::get()->tag("div", Translator::get()->translate("[DELETED BY MODERATOR]"), ['class' => "forum-reply-deleted-message"]);
            } else {
                return Html::get()->tag("div", Translator::get()->translate("[DELETED]"), ['class' => "forum-reply-deleted-message"]);
            }
        }
        return nl2br(ForumTextarea::parseText($this->content, PageTag::getTagRules(), [
            'linkRoot' => WebApp::get()->request()->getLinkRoot(),
            'webRoot' => WebApp::get()->request()->getWebRoot()
        ])) . Html::get()->scriptFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/highlight.pack.js') .
        Html::get()->cssFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/styles/github.css') .
        Html::get()->script('hljs.tabReplace = \'    \';hljs.initHighlightingOnLoad();');
    }

    /**
     * It will save thread info(update with last reply info) + subcategory info
     * @param int $sectionId
     * @param int $level
     * @return bool
     */
    public function saveReply($sectionId, $level = 1) {
        $this->user_id = WebApp::get()->user()->id;
        $this->time = date('Y-m-d H:i:s');
        $this->score = 0;
        $this->user_group_id = UserAccess::get()->getUserGroup($sectionId, true);
        if (!$this->save()) {
            return false;
        }
        $thread = ForumThread::findByPk($this->thread_id);
        $thread->replies = ($firstLevelReplies = ForumReply::countByAttributes(['thread_id' => $this->thread_id]))
            + ForumReplySecond::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplyThird::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplyForth::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplyFifth::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplySixth::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplySeventh::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplyEighth::countByAttributes(['thread_id' => $this->thread_id])
            + ForumReplyNth::countByAttributes(['thread_id' => $this->thread_id]);
        $thread->first_level_replies =  $firstLevelReplies;
        $thread->last_reply_id = $this->id;
        $thread->last_reply_user_id = $this->user_id;
        $thread->last_reply_level = $level;
        $thread->last_reply_date = $this->time;
        $thread->save(false);
        $thread->subcategory->last_active_thread_id = $thread->id;
        $thread->subcategory->last_activity_time = $this->time;
        $thread->subcategory->last_activity = 'reply';
        $thread->subcategory->last_active_user_id = $this->user_id;
        $replies = $this->_db->table(ForumThread::getTableName())->fields("SUM(replies) as `replies`")
            ->where("subcategory_id = :id")->setParam(":id", $thread->subcategory_id)->first();
        $thread->subcategory->number_of_replies = $replies ? $replies['replies'] : 0;
        $thread->subcategory->save();
        return true;
    }

    /**
     * Adds extra info required by edit.
     * @param string $content
     * @return bool
     */
    public function updateReply($content) {
        $this->content = $content;
        $this->edit_user_id = WebApp::get()->user()->id;
        $this->edit_time = date('Y-m-d H:i:s');
        $this->edited = 1;
        return $this->save();
    }

    /**
     * @var bool
     */
    protected $_canEdit;

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @param ForumThread $thread
     * @return bool
     */
    public function canEdit($categoryId = null, $sectionId = null, ForumThread $thread = null) {
        if ($this->deleted)
            return false;
        $thread = $thread?:$this->thread;
        $categoryId = $categoryId ?: $thread->subcategory->category_id;
        $sectionId = !is_null($sectionId) ? $sectionId : $thread->subcategory->category->section_id;
        if (WebApp::get()->user()->isGuest()) { //added a fix for guests;
            return false;
        }

        if ($this->user_id == WebApp::get()->user()->id && (!($this->deleted || UserAccess::get()->isMuted($sectionId) || $thread->closed))) {
            return true;
        }
        if (!is_null($this->_canEdit)) {
            return $this->_canEdit;
        }
        return $this->_canEdit = UserAccess::get()->isCategoryModerator($categoryId, $sectionId);
    }

    public function getAuthorIcon() {
        return Config::value('USER_ICON_FOLDER_URL') . ($this->author->icon ?: 'default.png');
    }

    public function hasReplies() {
        return (bool)$this->replies;
    }

    /**
     * Check my vote status for selected reply
     * @return bool|string
     */
    public function getMyVote(){
        if (!$this->myVote || !$this->myVote->user_id){ //until empty loaded is fix I check for user_id also
            return false;
        }
        if ($this->myVote->vote == 0){
            return "negative";
        } elseif ($this->myVote->vote == 1) {
            return "positive";
        }
    }
}
