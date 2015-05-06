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
    /**
     * @param $search
     * @param null|int[] $categorie
     * @param null|int[] $subcategorie
     * @param null|int[] $autor
     * @param int $currentPage
     */
    public function actionIndex($search, $categorie = null, $subcategorie = null, $autor = null, $currentPage = 1){
        $this->assign("threads", ForumThread::findAllByKeyWords($search, $this->sectionId, $categorie, $subcategorie, $autor, $currentPage));
        $this->assign("currentPage", $currentPage);
        $this->assign("numberOfThreads", ForumThread::countAllByKeyWords($search, $this->sectionId, $categorie, $subcategorie, $autor, $currentPage));
    }

    public function actionAdvanced(){

    }

    public function actionRecent(){
        $this->assign("threads", ForumThread::findRecent($this->sectionId));
    }
}