<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.01.2016
 * Time: 14:23
 */

namespace mpf\modules\blog\controllers;


use app\components\htmltools\Messages;
use mpf\modules\blog\components\BlogConfig;
use mpf\modules\blog\components\Controller;
use mpf\modules\blog\models\BlogPost;
use mpf\WebApp;

class Articles extends Controller
{

    public $subTitle = 'Manage Articles';

    public function actionIndex()
    {
        $this->assign('model', BlogPost::model()->setAttributes(isset($_GET['BlogPost']) ? $_GET['BlogPost'] : []));
    }

    public function actionAdd()
    {
        $article = new BlogPost();
        $article->author_id = WebApp::get()->user()->id;
        $article->time_written = date('Y-m-d H:i:s');
        $article->status = BlogPost::STATUS_NEW;
        if (isset($_POST['BlogPost']) && $article->setAttributes($_POST['BlogPost'])->save()) {
            $article->afterSave();
            $this->goToPage('home', 'read', ['id' => $article->id, 'title' => $article->url]);
        }
        $this->assign('model', $article);
        Messages::get()->info("Add " . BlogConfig::get()->introductionSeparator . " to set the limit for text displayed on the articles list!");
    }

    public function actionEdit($id)
    {
        $article = BlogPost::findByPk($id);
        $article->edited_by = WebApp::get()->user()->id;
        $article->edit_time = date('Y-m-d H:i:s');
        $article->edit_number += 1;
        if (isset($_POST['BlogPost']) && $article->setAttributes($_POST['BlogPost'])->save()) {
            $article->afterSave();
            $this->goToPage('home', 'read', ['id' => $article->id, 'title' => $article->url]);
        }
        $this->assign('model', $article);
        Messages::get()->info("Add " . BlogConfig::get()->introductionSeparator . " to set the limit for text displayed on the articles list!");
    }

    public function actionPublish(){
        $model = BlogPost::findByPk($_POST['BlogPost']);
        $model->status = BlogPost::STATUS_PUBLISHED;
        $model->time_published = date('Y-m-d H:i:s');
        $model->save();
        $this->goToPage('home', 'read', ['id' => $model->id, 'title' => $model->url]);
    }

    public function actionDelete()
    {
        $model = BlogPost::findByPk($_POST['BlogPost']);
        $model->status = BlogPost::STATUS_DELETED;
        $model->save();
        $this->goToAction('index');
    }
}