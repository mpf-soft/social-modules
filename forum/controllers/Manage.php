<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.03.2015
 * Time: 13:25
 */

namespace app\modules\forum\controllers;


use app\components\htmltools\Messages;
use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumCategory;
use app\modules\forum\models\ForumSubcategory;
use app\modules\forum\models\ForumUserGroup;
use mpf\WebApp;

class Manage extends Controller {
    public function actionGroups() {
        $model = ForumUserGroup::model();
        if (isset($_GET['ForumUserGroup'])){
            $model->setAttributes($_GET['ForumUserGroup']);
        }
        $this->assign('model', $model);
    }

    public function actionNewGroup(){

    }

    public function actionEditGroup($id){

    }

    public function actionCategories() {
        if (isset($_POST['name'])){
            if (!ForumUserGroup::countByAttributes(['section_id' => $this->sectionId])){
                Messages::get()->error("No groups created yet! First add a group before creating categories!");
                $this->request->goBack();
            }
            $category = new ForumCategory();
            $category->name = $_POST['name'];
            $category->section_id = $this->sectionId;
            $category->user_id = WebApp::get()->user()->id;
            $category->url_friendly_name = urlencode(str_replace(['?', "'", '"', '/', '\\', '%', ' '], '-', $_POST['name']));
            if ($category->save()){
                $this->goToAction('category', ['id' => $category->id]);
            } else {
                Messages::get()->error(implode("\n", $category->getErrors('name')));
                $this->request->goBack();
            }
        }
        $model = ForumSubcategory::model();
        if (isset($_GET['ForumSubcategory'])){
            $model->setAttributes($_GET['ForumSubcategory']);
        }
        $this->assign('model', $model);
    }

    public function actionCategory($id){
        $model = ForumCategory::findByPk($id);
        if (isset($_POST['ForumCategory'])){
            $model->setAttributes($_POST['ForumCategory']);
            if ($model->save()){
                Messages::get()->success("Category saved!");
                $this->request->goBack();
            }
        }
        $this->assign('model', $model);
    }

    public function actionUsers($group) {

    }

}