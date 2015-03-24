<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:18
 */

namespace app\modules\forum\controllers;


use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumCategory;

class Category extends Controller{

    public function actionIndex($id){
        $this->assign("category", ForumCategory::findByPk($id));
    }

}