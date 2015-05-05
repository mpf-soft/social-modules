<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:18
 */

namespace mpf\modules\forum\controllers;


use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumCategory;

class Category extends Controller{

    public function actionIndex($id){
        $this->assign("category", ForumCategory::findByPk($id));
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionId, true));
    }

}