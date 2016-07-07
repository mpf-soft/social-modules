<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.01.2016
 * Time: 14:24
 */

namespace mpf\modules\blog\controllers;


use app\components\htmltools\Messages;
use app\components\htmltools\Translator;
use mpf\modules\blog\components\Controller;
use mpf\modules\blog\models\BlogCategory;

class Categories extends Controller
{
    public $subTitle = 'Manage Categories';

    public function actionIndex()
    {
        $this->assign('model', BlogCategory::model()->setAttributes(isset($_GET['BlogCategory']) ? $_GET['BlogCategory'] : []));
    }

    public function actionCreate()
    {
        $this->goToAction('add');
    }

    public function actionAdd()
    {
        $model = new BlogCategory();
        if (isset($_POST['BlogCategory']) && $model->setAttributes($_POST['BlogCategory'])->save()) {
            $model->saveTranslations();
            Messages::get()->success(Translator::get()->t('Category saved!'));
            $this->goToAction('index');
        }
        $this->assign('model', $model);
    }

    public function actionEdit($id)
    {
        $model = BlogCategory::findByPk($id);
        $model->reloadTranslations();
        if (isset($_POST['BlogCategory']) && $model->setAttributes($_POST['BlogCategory'])->save()) {
            $model->saveTranslations();
            Messages::get()->success(Translator::get()->t('Category saved!'));
            $this->goToAction('index');
        }
        $this->assign('model', $model);
    }

    public function actionDelete()
    {

    }
}