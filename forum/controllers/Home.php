<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 14:48
 */

namespace app\modules\forum\controllers;

use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumCategory;

class Home extends Controller{

    public function actionIndex(){
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionID));
    }
}