<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 06.05.2015
 * Time: 09:15
 */

namespace mpf\modules\blog\controllers;


use mpf\modules\blog\components\Controller;
use mpf\modules\blog\models\BlogPost;

class Home extends Controller
{

    public function actionIndex()
    {
        $this->assign('articles', BlogPost::getLatestPublished(isset($_GET['page']) ? $_GET['page'] : 1));
    }

    public function actionCategory($id)
    {
        $this->assign('articles', BlogPost::getForCategory($id, isset($_GET['page']) ? $_GET['page'] : 1));
    }

    public function actionSearch($text)
    {
        $this->assign('articles', BlogPost::getForSearch($text, isset($_GET['page']) ? $_GET['page'] : 1));
    }

    public function actionRead($id)
    {
        $this->assign('article', BlogPost::findByPk($id));
    }

}