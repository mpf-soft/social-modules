<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:19
 */

namespace mpf\modules\forum\controllers;


use mpf\modules\forum\components\Config;
use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumCategory;
use mpf\modules\forum\models\ForumSubcategory;
use mpf\modules\forum\models\ForumThread;

class Subcategory extends Controller{

    public function actionIndex($id, $page = 1){
        $page = ($page < 1) ? 1 : $page;
        $this->assign("subcategory", ForumSubcategory::findByPk($id));
        $this->assign('threads', ForumThread::findAllForSubcategory($id, $page, Config::value('FORUM_THREADS_PER_PAGE')));
        $this->assign('currentPage', $page);
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionId, true));
    }

}