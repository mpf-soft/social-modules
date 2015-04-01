<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 31.03.2015
 * Time: 15:03
 */

namespace mpf\modules\forum\controllers;


use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumUser2Section;

class Members extends Controller{
    public function actionIndex(){
        $model = ForumUser2Section::model();
        if (isset($_GET['ForumUser2Section'])){
            $model->setAttributes($_GET['ForumUser2Section']);
        }
        $this->assign("model", $model);
    }
}