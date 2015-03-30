<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:18
 */

namespace app\modules\forum\controllers;


use app\components\htmltools\Messages;
use app\modules\forum\components\Controller;
use app\modules\forum\components\UserAccess;
use app\modules\forum\models\ForumReply;
use app\modules\forum\models\ForumSubcategory;
use app\modules\forum\models\ForumThread;
use mpf\WebApp;

class Thread extends Controller{

    public function actionIndex($id, $page = 1){
        $thread = ForumThread::findByPk($id);
        if ($thread->subcategory->category->section_id != $this->sectionId){
            $this->goToPage("special", "notFound");
            return;
        }
        $this->assign("thread", $thread);
        $this->assign("subcategory", $thread->subcategory);
        $this->assign("currentPage", $page);
        $this->assign("replies", ForumReply::findAllRepliesForThread($id, $page, $this->repliesPerPage));
        $replyModel = new ForumReply();
        $replyModel->thread_id = $id;
        if (isset($_POST['ForumReply']) && !$thread->closed && UserAccess::get()->canReplyToThread($thread->subcategory->category_id, $this->sectionId)){
            if ($replyModel->saveReply($_POST['ForumReply']['content'], $this->sectionId)){
                Messages::get()->success("Reply saved!");
                $this->goBack();
            }
        }
        $this->assign('replyModel', $replyModel);
    }

    public function actionNew($subcategory){
        $subcategory = ForumSubcategory::findByPk($subcategory);
        if ($subcategory->category->section_id != $this->sectionId){
            $this->goToPage("special", "notFound");
            return false;
        }
        if (!UserAccess::get()->canCreateNewThread($subcategory->category_id, $this->sectionId)){
            Messages::get()->error("Can't start a new thread in this category!");
            $this->goToPage('special', 'accessDenied');
            return false;
        }
        $thread = new ForumThread();
        $thread->subcategory_id = $subcategory->id;
        $thread->user_id = WebApp::get()->user()->id;
        if (isset($_POST['ForumThread'])){
            if (isset($_POST['sticky']) && UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)){
                $thread->sticky = 1;
            }
            $thread->setAttributes($_POST['ForumThread']);
            if ($thread->save() && $thread->publishNew($subcategory)){
                $this->goToAction('index', ['id' => $thread->id]);
            }
        }
        $this->assign('subcategory', $subcategory);
        $this->assign('model', $thread);
    }

    public function actionEdit($id){

    }

    public function actionEditReply($id){

    }

    public function actionMove($id){

    }
}