<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:19
 */

namespace app\modules\forum\controllers;


use app\modules\forum\components\Config;
use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumSubcategory;
use app\modules\forum\models\ForumThread;

class Subcategory extends Controller{

    public function actionIndex($id, $page = 1){
        $page = ($page < 1) ? 1 : $page;
        $this->assign("subcategory", ForumSubcategory::findByPk($id));
        $this->assign('threads', ForumThread::findAllForSubcategory($id, $page, Config::value('FORUM_THREADS_PER_PAGE')));
        $this->assign('currentPage', $page);
    }

}