<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 06.05.2015
 * Time: 09:15
 */

namespace mpf\modules\blog\controllers;


use app\components\htmltools\Messages;
use mpf\modules\blog\components\Controller;
use mpf\modules\blog\models\BlogComment;
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
        $this->assign('article', $art = BlogPost::findByPk($id));
        if ($art->allow_comments) {
            $model = new BlogComment;
            $model->post_id = $id;
            if (isset($_POST['BlogComment']) && $model->setAttributes($_POST['BlogComment'])->save()) {
                Messages::get()->success("Comment saved!");
                $this->goBack();
            }
            $this->assign('model', $model);
            $this->assign('comments', BlogComment::findAllByAttributes(['post_id' => $id, 'status' => BlogComment::STATUS_OK]));
        }
    }

}