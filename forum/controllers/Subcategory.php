<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:19
 */

namespace mpf\modules\forum\controllers;


use app\components\htmltools\Messages;
use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumCategory;
use mpf\modules\forum\models\ForumSubcategory;
use mpf\modules\forum\models\ForumThread;

class Subcategory extends Controller{

    public function actionIndex($id = null, $page = 1){
        if (is_null($id)){
            Messages::get()->error('Invalid request!');
            $this->goToPage('home', 'index');
        }
        $page = ($page < 1) ? 1 : $page;
        $subcategory = ForumSubcategory::findByPk($id);
        if (is_null($subcategory)){
            Messages::get()->error('Invalid request! Subcategory not found!');
            $this->goToPage('home', 'index');
        }
        $this->assign("subcategory", $subcategory);
        $this->assign('threads', ForumThread::findAllForSubcategory($id, $page));
        $this->assign('currentPage', $page);
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionId, true));
    }

}