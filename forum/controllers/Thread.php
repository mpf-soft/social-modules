<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:18
 */

namespace mpf\modules\forum\controllers;


use app\components\htmltools\Messages;
use mpf\modules\forum\components\Config;
use mpf\modules\forum\components\Controller;
use mpf\modules\forum\components\ModelHelper;
use mpf\modules\forum\components\Translator;
use mpf\modules\forum\components\UserAccess;
use mpf\modules\forum\models\ForumReply;
use mpf\modules\forum\models\ForumReplyNth;
use mpf\modules\forum\models\ForumSubcategory;
use mpf\modules\forum\models\ForumThread;
use mpf\WebApp;

class Thread extends Controller {

    public function actionVoteThread(){
        $thread = ForumThread::findByPk($_POST['id']);
        if (!UserAccess::get()->canRead($thread->section_id, $thread->category_id)){
            die($thread->score . ' ' . Translator::get()->translate("points") . ':0');
        }
        $oldVote = WebApp::get()->sql()->table("forum_thread_votes")->where("thread_id=:id AND user_id=:user")
                    ->setParams([':id' => $_POST['id'], ':user' => WebApp::get()->user()->id])
                    ->first();
        $voteType = ($_POST['type'] == 'agree' ? '1' : '0');
        $resultVoted = 0;
        if ($oldVote && $voteType == $oldVote['vote']) { //remove vote;
            WebApp::get()->sql()->table('forum_thread_votes')->where("thread_id=:id AND user_id=:user")
                ->setParams([':id' => $_POST['id'], ':user' => WebApp::get()->user()->id])->delete();
        } else { //add or change vote
            $resultVoted = 1;
            WebApp::get()->sql()->table('forum_thread_votes')->insert([
                'thread_id' => $_POST['id'],
                'user_id' => WebApp::get()->user()->id,
                'time' => date('Y-m-d H:i:s'),
                'vote' => $voteType
            ], [
                'vote' => $voteType,
                'time' => date('Y-m-d H:i:s')
            ]); //insert or update vote type and time;
        }
        //recount;
        $thread->score = WebApp::get()->sql()->table('forum_thread_votes')->where("thread_id=:id and vote=1")
                ->setParams([':id' => $_POST['id']])->count() -
            WebApp::get()->sql()->table('forum_thread_votes')->where("thread_id=:id AND vote=0")
                ->setParams([':id' => $_POST['id']])->count();
        $thread->save(false);
        die($thread->score . ' ' . Translator::get()->translate("points") .  ':' . ($resultVoted ? '1' : '0'));
    }

    public function actionVote() {
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth', 'ForumReplySeventh', 'ForumReplyEighth',
            'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth'];
        $model = "\\mpf\\modules\\forum\\models\\" . $models[$_POST['level']];
        $entry = $model::findByPk($_POST['id']);
        /* @var $entry \mpf\modules\forum\models\ForumReply */

        if (!UserAccess::get()->canRead($entry->section_id, $entry->thread->category_id)) {
            die($entry->score . ' ' . Translator::get()->translate("points") . ':0');
        }
        $voted = WebApp::get()->sql()->table('forum_reply_votes')->where("reply_id=:id AND level=:lvl AND user_id=:user")
            ->setParams([':id' => $_POST['id'], ':lvl' => $_POST['level'], ':user' => WebApp::get()->user()->id])
            ->first();
        $voteType = ($_POST['type'] == 'agree' ? '1' : '0');
        $resultVoted = 0;
        if ($voted && $voteType == $voted['vote']) { //remove vote;
            WebApp::get()->sql()->table('forum_reply_votes')->where("reply_id=:id AND level=:lvl AND user_id=:user")
                ->setParams([':id' => $_POST['id'], ':lvl' => $_POST['level'], ':user' => WebApp::get()->user()->id])->delete();
        } else { //add or change vote
            $resultVoted = 1;
            WebApp::get()->sql()->table('forum_reply_votes')->insert([
                'reply_id' => $_POST['id'],
                'level' => $_POST['level'],
                'user_id' => WebApp::get()->user()->id,
                'time' => date('Y-m-d H:i:s'),
                'vote' => $voteType
            ], [
                'vote' => $voteType,
                'time' => date('Y-m-d H:i:s')
            ]); //insert or update vote type and time;
        }
        //recount;
        $entry->score = WebApp::get()->sql()->table('forum_reply_votes')->where("reply_id=:id AND level=:lvl and vote=1")
                ->setParams([':id' => $_POST['id'], ':lvl' => $_POST['level']])->count() -
            WebApp::get()->sql()->table('forum_reply_votes')->where("reply_id=:id AND level=:lvl and vote=0")
                ->setParams([':id' => $_POST['id'], ':lvl' => $_POST['level']])->count();
        $entry->save(false);
        die($entry->score . ' ' . Translator::get()->translate("points") .  ':' . ($resultVoted ? '1' : '0'));
    }

    public function actionReply() {
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth', 'ForumReplySeventh', 'ForumReplyEighth',
            'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth'];
        if (isset($_POST['ForumReply'])) {
            $model = $models[$_POST['level']];
            $_POST[$model] = $_POST['ForumReply'];
            if ('ForumReply' != $model)
                unset($_POST['ForumReply']);
        }
        $noneFound = true;
        foreach ($models as $modelClass) {
            if (!isset($_POST[$modelClass]))
                continue;
            $noneFound = false;
            $model = '\mpf\modules\forum\models\\' . $modelClass;
            $model = new $model();
            /* @var $model \mpf\modules\forum\models\ForumReplyNth */
            $model->setAttributes($_POST[$modelClass]);
            $model->thread_id = $_POST['thread_id'];
            if ($_POST['parent']) {
                $model->reply_id = $_POST['parent'];
            }
            if ($_POST['level'] >= 9) { // for nth levels also save the level itself
                $model->level = $_POST['level'];
            }
            if ($_POST['level'] >= 10) { // from here nthreply receives that id;
                $parent = ForumReplyNth::findByPk($_POST['parent']);
                $model->reply_id = $parent->reply_id;
                $model->nthreply_id = $_POST['parent'];
            }
            $thread = ForumThread::findByPk($model->thread_id);
            $thread->subscribe();
            if (!$thread || $thread->closed || !UserAccess::get()->canReplyToThread($thread->subcategory->category_id, $thread->subcategory->category->section_id)) {
                Messages::get()->error("Can't reply to this thread!");
                $this->goBack();
                return;
            }
            if ($model->saveReply($thread->subcategory->category->section_id, $_POST['level'])) {
                Messages::get()->success("Reply saved!");
                $thread->newNotification($this->sectionId, 'new');
                $this->goToAction('index', ['id' => $model->thread_id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name]);
            }
            $this->assign("model", $model);
        }
        if ($noneFound) {
            $this->goToPage('forum', 'index');
        }
    }


    public function actionIndex($id, $page = 1) {
        $thread = ForumThread::findByPk($id, ['with' => ['subcategory', 'subcategory.category', 'owner']]);
        if (!$thread){
            $this->goToPage("special", "notFound");
            return false;
        }
        if ($thread->deleted && (!$thread->canEdit())){
            Messages::get()->error("Thread deleted!");
            $this->goBack();
            return false;
        } elseif ($thread->deleted){
            Messages::get()->info("This Thread is DELETED!");
        }
        ForumReply::$currentSection = 0;
        if ($thread->subcategory->category->section_id != $this->sectionId) {
            $this->goToPage("special", "notFound");
            return;
        }
        if (isset($_GET['subscribe'])){
            $thread->subscribe();
            $this->goBack();
        } elseif (isset($_GET['unsubscribe'])){
            $thread->unsubscribe();
            $this->goBack();
        }
        $thread->updateViews();
        $this->assign("thread", $thread);
        $this->assign("subcategory", $thread->subcategory);
        $this->assign("currentPage", $page);
        $this->assign("replies", ForumReply::findAllRepliesForThread($id, $page, Config::value('FORUM_REPLIES_PER_PAGE')));

        $replyModel = new ForumReply();
        $replyModel->thread_id = $id;
        $this->assign('replyModel', $replyModel);
    }

    public function actionNew($subcategory) {
        $subcategory = ForumSubcategory::findByPk($subcategory);
        if ($subcategory->category->section_id != $this->sectionId) {
            $this->goToPage("special", "notFound");
            return false;
        }
        if (!UserAccess::get()->canCreateNewThread($subcategory->category_id, $this->sectionId)) {
            Messages::get()->error("Can't start a new thread in this category!");
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread = new ForumThread();
        $thread->subcategory_id = $subcategory->id;
        $thread->category_id = $subcategory->category_id;
        $thread->section_id = $subcategory->category->section_id;
        $thread->user_id = WebApp::get()->user()->id;
        if (isset($_POST['ForumThread'])) {
            if (isset($_POST['sticky']) && UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)) {
                $thread->sticky = 1;
            }
            $thread->setAttributes($_POST['ForumThread']);
            if ($thread->save() && $thread->publishNew($subcategory)) {
                $this->goToAction('index', ['id' => $thread->id, 'subcategory' => $subcategory->url_friendly_title, 'category' => $subcategory->category->url_friendly_name]);
            }
        }
        $this->assign('subcategory', $subcategory);
        $this->assign('model', $thread);
    }

    public function actionEdit($id) {
        $thread = ForumThread::findByPk($id);
        if (!$thread->canEdit()) {
            $this->goToPage("special", "accessDenied");
            return false;
        }
        if (isset($_POST['ForumThread'])) {
            if (isset($_POST['sticky']) && UserAccess::get()->isCategoryModerator($thread->subcategory->category_id, $thread->subcategory->category->section_id)) {
                $thread->sticky = 1;
            }
            $thread->setAttributes($_POST['ForumThread']);
            $thread->edit_time = date('Y-m-d H:i:s');
            $thread->edit_user_id = WebApp::get()->user()->id;
            if ($thread->save()) {
                $thread->newNotification($this->sectionId, 'editThread');
                $this->goToAction('index', ['id' => $thread->id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name]);
            }
        }

        $this->assign('model', $thread);
    }

    public function actionEditReply($id, $level) {
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth'];
        $modelClass = $models[$level];
        $reply = "\\mpf\\modules\\forum\\models\\" . $modelClass;
        $reply = $reply::findByPk($id); /* @var $reply \mpf\modules\forum\models\ForumReply */
        if (!$reply) {
            $this->goBack();
        }
        if (!$reply->canEdit()) {
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        if (isset($_POST[$modelClass])) {
            if ($reply->updateReply($_POST[$modelClass]['content'])) {
                $reply->thread->newNotification($this->sectionId, 'editReply');
                Messages::get()->success("Reply updated!");
                $this->goToAction('index', ['id' => $reply->thread_id,
                    'subcategory' => $reply->thread->subcategory->url_friendly_title,
                    'category' => $reply->thread->subcategory->category->url_friendly_name]);
            }
        }
        $this->assign("model", $reply);
    }

    public function actionDeleteThread(){
        $thread = ForumThread::findByPk($_POST['id']);
        if (!$thread->canEdit()){
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread->deleted = 1;
        $thread->deleted_time = date('Y-m-d H:i:s');
        $thread->deleted_user_id = WebApp::get()->user()->id;
        $thread->save();
        Messages::get()->success("Thread deleted!");
        $this->goBack();

    }

    public function actionDeleteReply() {
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth'];
        $modelClass = $models[$_POST['level']];
        $reply = "\\mpf\\modules\\forum\\models\\" . $modelClass;
        $reply = $reply::findByPk($_POST['id']);
        if (!$reply) {
            $this->goBack();
        }
        if (!$reply->canEdit()) {
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $reply->deleted = 1;
        $reply->deleted_time = date('Y-m-d H:i:s');
        $reply->deleted_user_id = WebApp::get()->user()->id;
        $reply->save();
        Messages::get()->success("Reply deleted!");
        $this->goBack();
    }

    public function actionMove($id) {
        $thread = ForumThread::findByPk($id);
        if (!$thread->canEdit()) {
            $this->goToPage("special", "accessDenied");
            return false;
        }
        if (isset($_POST['ForumThread'])) {
            $old = $thread->subcategory_id;
            $thread->setAttributes($_POST['ForumThread']);
            if ($thread->save()) {
                $thread->afterMove($old, $this->updateURLWithSection(['thread', 'index', ['id' => $thread->id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name], $this->request->getModule()]));
                $this->goToAction('index', ['id' => $thread->id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name]);
            }
        }

        $this->assign('model', $thread);

    }

    public function actionClose($id) {
        $thread = ForumThread::findByPk($id);
        if (!UserAccess::get()->isCategoryModerator($thread->subcategory->category_id, $thread->subcategory->category->section_id)) {
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread->closed = $thread->closed ? 0 : 1;
        $thread->save();
        $this->goBack();
    }

    public function actionSticky($id) {
        $thread = ForumThread::findByPk($id);
        if (!UserAccess::get()->isCategoryModerator($thread->subcategory->category_id, $thread->subcategory->category->section_id)) {
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread->sticky = $thread->sticky ? 0 : 1;
        $thread->save();
        $this->goBack();
    }
}