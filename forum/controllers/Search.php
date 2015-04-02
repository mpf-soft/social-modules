<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.03.2015
 * Time: 11:15
 */

namespace mpf\modules\forum\controllers;


use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumThread;

class Search extends Controller{
    public function actionIndex(){

    }

    public function actionAdvanced(){

    }

    public function actionRecent(){
        $this->assign("threads", ForumThread::findRecent($this->sectionId));
    }
}