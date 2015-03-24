<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:18
 */

namespace app\modules\forum\controllers;


use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumSubcategory;
use app\modules\forum\models\ForumThread;

class Thread extends Controller{

    public function actionIndex($id, $page = 1){

    }

    public function actionNew($subcategory){
        $thread = new ForumThread();
        $thread->subcategory_id = $subcategory;
        if (isset($_POST['ForumThread'])){
            $thread->setAttributes($_POST['ForumThread']);
            if ($thread->save()){
                $this->goToAction('index', ['id' => $thread->id]);
            }
        }
        $this->assign('subcategory', ForumSubcategory::findByPk($subcategory));
        $this->assign('model', $thread);
    }

    public function actionEdit($id){

    }

    public function actionReply($id){

    }

    public function actionEditReply($id){

    }

    public function actionMove($id){

    }
}