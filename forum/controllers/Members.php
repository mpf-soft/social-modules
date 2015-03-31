<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 31.03.2015
 * Time: 15:03
 */

namespace app\modules\forum\controllers;


use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumUser2Section;

class Members extends Controller{
    public function actionIndex(){
        $model = ForumUser2Section::model();
        if (isset($_GET['ForumUser2Section'])){
            $model->setAttributes($_GET['ForumUser2Section']);
        }
        $this->assign("model", $model);
    }
}