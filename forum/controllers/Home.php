<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 14:48
 */

namespace mpf\modules\forum\controllers;

use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumCategory;

class Home extends Controller{

    public function actionIndex(){
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionId, true));
    }
}