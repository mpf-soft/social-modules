<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:19
 */

namespace app\modules\forum\controllers;


use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumSubcategory;

class Subcategory extends Controller{

    public function actionIndex($id){
        $this->assign("subcategory", ForumSubcategory::findByPk($id));
        $this->assign('threads', []);
    }

}