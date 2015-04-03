<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:18
 */

namespace mpf\modules\forum\controllers;


use app\components\htmltools\Messages;
use app\controllers\User;
use mpf\modules\forum\components\Config;
use mpf\modules\forum\components\Controller;
use mpf\modules\forum\components\UserAccess;
use mpf\modules\forum\models\ForumReply;
use mpf\modules\forum\models\ForumSubcategory;
use mpf\modules\forum\models\ForumThread;
use mpf\web\Session;
use mpf\WebApp;

class Thread extends Controller {

    public function actionReply(){
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth'];
        if (isset($_POST['ForumReply'])){
            $model = $models[$_POST['level']];
            $_POST[$model] = $_POST['ForumReply'];
            if ('ForumReply' != $model)
                unset($_POST['ForumReply']);
        }
        $noneFound = true;
        foreach ($models as $modelClass){
            if (!isset($_POST[$modelClass]))
                continue;
            $noneFound = false;
            $model = '\mpf\modules\forum\models\\' . $modelClass;
            $model = new $model(); /* @var $model \mpf\modules\forum\models\ForumReply */
            $model->setAttributes($_POST[$modelClass]);
            $model->thread_id = $_POST['thread_id'];
            if ($_POST['parent']){
                $model->reply_id = $_POST['parent'];
            }
            $thread = ForumThread::findByPk($model->thread_id);
            if (!$thread || $thread->closed || !UserAccess::get()->canReplyToThread($thread->subcategory->category_id, $thread->subcategory->category->section_id)){
                Messages::get()->error("Can't reply to this thread!");
                $this->goBack();
                return;
            }
            if ($model->saveReply($thread->subcategory->category->section_id)){
                Messages::get()->success("Reply saved!");
                $this->goToAction('index', ['id' => $model->thread_id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name]);
            }
            $this->assign("model", $model);
        }
        if ($noneFound){
            $this->goToPage('forum', 'index');
        }
    }


    public function actionIndex($id, $page = 1) {
        $thread = ForumThread::findByPk($id);
        if ($thread->subcategory->category->section_id != $this->sectionId) {
            $this->goToPage("special", "notFound");
            return;
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
        $thread->user_id = WebApp::get()->user()->id;
        if (isset($_POST['ForumThread'])) {
            if (isset($_POST['sticky']) && UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)) {
                $thread->sticky = 1;
            }
            $thread->setAttributes($_POST['ForumThread']);
            if ($thread->save() && $thread->publishNew($subcategory)) {
                $this->goToAction('index', ['id'=> $thread->id, 'subcategory' => $subcategory->url_friendly_title, 'category' => $subcategory->category->url_friendly_name]);
            }
        }
        $this->assign('subcategory', $subcategory);
        $this->assign('model', $thread);
    }

    public function actionEdit($id) {
        $thread = ForumThread::findByPk($id);
        if (!$thread->canEdit()){
            $this->goToPage("special", "accessDenied");
            return false;
        }
        if (isset($_POST['ForumThread'])){
            if (isset($_POST['sticky']) && UserAccess::get()->isCategoryModerator($thread->subcategory->category_id, $thread->subcategory->category->section_id)) {
                $thread->sticky = 1;
            }
            $thread->setAttributes($_POST['ForumThread']);
            $thread->edit_time = date('Y-m-d H:i:s');
            $thread->edit_user_id = WebApp::get()->user()->id;
            if ($thread->save()){
                $this->goToAction('index', ['id'=> $thread->id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name]);
            }
        }

        $this->assign('model', $thread);
    }

    public function actionEditReply($id, $level) {
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth'];
        $modelClass = $models[$level];
        $reply = "\\mpf\\modules\\forum\\models\\" . $modelClass;
        $reply = $reply::findByPk($id);
        if (!$reply) {
            $this->goBack();
        }
        if (!$reply->canEdit()) {
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        if (isset($_POST[$modelClass])) {
            if ($reply->updateReply($_POST[$modelClass]['content'])) {
                Messages::get()->success("Reply updated!");
                $this->goToAction('index', ['id' => $reply->thread_id,
                    'subcategory' => $reply->thread->subcategory->url_friendly_title,
                    'category' => $reply->thread->subcategory->category->url_friendly_name]);
            }
        }
        $this->assign("model", $reply);
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
        $reply->save();
        Messages::get()->success("Reply deleted!");
        $this->goBack();
    }

    public function actionMove($id) {
        $thread = ForumThread::findByPk($id);
        if (!$thread->canEdit()){
            $this->goToPage("special", "accessDenied");
            return false;
        }
        if (isset($_POST['ForumThread'])){
            $old = $thread->subcategory_id;
            $thread->setAttributes($_POST['ForumThread']);
            if ($thread->save()){
                $thread->afterMove($old);
                $this->goToAction('index', ['id'=> $thread->id, 'subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->subcategory->category->url_friendly_name]);
            }
        }

        $this->assign('model', $thread);

    }

    public function actionClose($id){
        $thread = ForumThread::findByPk($id);
        if (!UserAccess::get()->isCategoryModerator($thread->subcategory->category_id, $thread->subcategory->category->section_id)){
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread->closed = $thread->closed?0:1;
        $thread->save();
        $this->goBack();
    }

    public function actionSticky($id){
        $thread = ForumThread::findByPk($id);
        if (!UserAccess::get()->isCategoryModerator($thread->subcategory->category_id, $thread->subcategory->category->section_id)){
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread->sticky = $thread->sticky?0:1;
        $thread->save();
        $this->goBack();
    }
}