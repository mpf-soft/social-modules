<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.03.2015
 * Time: 13:25
 */

namespace mpf\modules\forum\controllers;


use app\components\htmltools\Messages;
use app\models\User;
use app\models\UserTitle;
use mpf\modules\forum\components\Controller;
use mpf\modules\forum\components\UserAccess;
use mpf\modules\forum\models\ForumCategory;
use mpf\modules\forum\models\ForumSection;
use mpf\modules\forum\models\ForumSubcategory;
use mpf\modules\forum\models\ForumTitle;
use mpf\modules\forum\models\ForumUser2Section;
use mpf\modules\forum\models\ForumUserGroup;
use mpf\helpers\ArrayHelper;

class Manage extends Controller {
    /**
     * Before running any action it will check if user has access to edit current section. To edit subcategories for
     * a single category other admins will have access to a special smaller interface(or maybe this one in the future).
     * @param $actionName
     * @return bool
     */
    public function beforeAction($actionName){
        parent::beforeAction($actionName);
        if (!UserAccess::get()->isSectionAdmin($this->sectionId)){
            Messages::get()->error("You don't have the required rights to access this section!");
            $this->goToPage('special', 'accessDenied');
            die();
        }
        return true;
    }

    /**
     * Edit groups, change default group and delete groups.
     */
    public function actionGroups() {
        if (isset($_POST['changedefaultvisitorsgroup'])){
            if (ForumSection::findByPk($this->sectionId)->setDefaultGroup($_POST['group'], 'visitors')){
                Messages::get()->success("Default group updated!");
                $this->goBack();
            }
        }
        if (isset($_POST['changedefaultmembersgroup'])){
            if (ForumSection::findByPk($this->sectionId)->setDefaultGroup($_POST['group'], 'members')){
                Messages::get()->success("Default group updated!");
                $this->goBack();
            }
        }
        $model = ForumUserGroup::model();
        if (isset($_GET['ForumUserGroup'])){
            $model->setAttributes($_GET['ForumUserGroup']);
        }
        $this->assign('model', $model);
    }

    public function actionNewGroup(){
        $model = new ForumUserGroup();
        $model->section_id = $this->sectionId;
        if (!UserAccess::get()->isSectionAdmin($model->section_id)){
            Messages::get()->error("Not enough rights to add a new user group for this forum!");
            $this->goBack();
            die();
        }
        if (isset($_POST['ForumUserGroup'])){
            $model->setAttributes($_POST['ForumUserGroup']);
            if ($model->save()){
                Messages::get()->success("Group saved!");
                $this->goToAction('groups');
            }
        }
        $this->setPageLayout('group');
        $this->assign('model', $model);
    }

    public function actionEditGroup($id){
        $model = ForumUserGroup::findByPk($id);
        if (!UserAccess::get()->isSectionAdmin($model->section_id)){
            Messages::get()->error("Not enough rights to edit this user group!");
            $this->goBack();
            die();
        }
        if (isset($_POST['ForumUserGroup'])){
            $model->setAttributes($_POST['ForumUserGroup']);
            if ($model->save()){
                Messages::get()->success("Group saved!");
                $this->goToAction('groups');
            }
        }
        $this->setPageLayout('group');
        $this->assign('model', $model);
    }

    public function actionCategories() {
        if (!ForumUserGroup::countByAttributes(['section_id' => $this->sectionId])){
            Messages::get()->error("No groups created yet! First add a group before creating categories!");
        }
        $model = ForumCategory::model();
        if (isset($_GET['ForumCategory'])){
            $model->setAttributes($_GET['ForumCategory']);
        }
        $this->assign('model', $model);
    }

    public function actionNewCategory(){
        $model = new ForumCategory();
        $model->section_id = $this->sectionId;
        $model->icon = "default.png";
        if (isset($_POST['ForumCategory'])){
            $model->setAttributes($_POST['ForumCategory']);
            if ($model->save()){
                if (false != ($icon = $this->uploadImage("categories", 'icon', $model->id))){
                    if ($model->icon &&  $model->icon != "default.png"){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                    $model->icon = $icon;
                    if (!$model->save()){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                }
                $model->updateGroupRights();
                Messages::get()->success("Category saved!");
                $this->goToAction('categories');
            }
        }
        $this->setPageLayout('category');
        $this->assign('model', $model);
    }

    public function actionEditCategory($id){
        $model = ForumCategory::findByPk($id);
        if (isset($_POST['ForumCategory'])){
            $model->setAttributes($_POST['ForumCategory']);
            if ($model->save()){
                if (false != ($icon = $this->uploadImage("categories", 'icon', $model->id))){
                    if ($model->icon &&  $model->icon != "default.png"){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                    $model->icon = $icon;
                    if (!$model->save()){
                        Messages::get()->error("Error saving new icon!");
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                }
                $model->updateGroupRights();
                Messages::get()->success("Category saved!");
             //   $this->goToAction('categories');
            }
        }
        $this->setPageLayout('category');
        $model->reloadGroupRights();
        $this->assign('model', $model);
    }

    public function actionSubcategories($category){
        $category = ForumCategory::findByPk($category);
        if (!UserAccess::get()->isSectionAdmin($category->section_id)){
            Messages::get()->error("Not enough rights to edit subcategories for this category!");
            $this->goBack();
            die();
        }
        $model = ForumSubcategory::model();
        if (isset($_GET['ForumSubcategory'])){
            $model->setAttributes($_GET['ForumSubcategory']);
        }
        $this->assign('model', $model);
        $this->assign('category', $category);
    }

    public function actionNewSubcategory($category = null){
        $model  =new ForumSubcategory();
        if ($category) {
            $model->category_id = $category;
        }
        $model->icon = "default.png";
        if (isset($_POST['ForumSubcategory'])){
            $model->setAttributes($_POST['ForumSubcategory']);
            if ($model->save()){
                if (false != ($icon = $this->uploadImage("subcategories", 'icon', $model->id))){
                    if ($model->icon &&  $model->icon != "default.png"){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                    $model->icon = $icon;
                    if (!$model->save()){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                }
                Messages::get()->success("Subcategory saved!");
                $this->goToAction('subcategories', ['category' => $model->category_id]);
            }
        }
        $this->assign('model', $model);
        $this->setPageLayout('subcategory');
    }

    public function actionEditSubcategory($id){
        $model = ForumSubcategory::findByPk($id);
        if (isset($_POST['ForumSubcategory'])){
            $model->setAttributes($_POST['ForumSubcategory']);
            if ($model->save()){
                if (false != ($icon = $this->uploadImage("subcategories", 'icon', $model->id))){
                    if ($model->icon &&  $model->icon != "default.png"){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                    $model->icon = $icon;
                    if (!$model->save()){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                }
                Messages::get()->success("Subcategory saved!");
                $this->goToAction('subcategories', ['category' => $model->category_id]);
            }
        }
        $this->setPageLayout('subcategory');
        $this->assign('model', $model);
    }


    public function actionUsers() {
        foreach (['muted', 'banned', 'group_id', 'title_id'] as $field){
            if (isset($_POST[$field])){
                ForumUser2Section::update($_POST['id'], [$field => $_POST[$field]]);
                Messages::get()->success("User saved!");
                $this->goBack();
            }
        }
        $model = ForumUser2Section::model();
        if (isset($_GET['ForumUser2Section'])){
            $model->setAttributes($_GET['ForumUser2Section']);
        }
        $this->assign('model', $model);
        $this->assign('groups', ArrayHelper::get()->transform(ForumUserGroup::findAllBySection($this->sectionId), ['id' => 'full_name']));
        $this->assign('titles', ArrayHelper::get()->transform(ForumTitle::findAll(), ['id' =>'title']));
    }

    public function actionMute(){
        if ('mute' == $_POST['action']){

        } elseif ('unmute'== $_POST['action']){

        }
    }

    public function actionTitles(){
        $model = ForumTitle::model();
        if (isset($_GET['ForumTitle'])){
            $model->setAttributes($_GET['ForumTitle']);
        }
        $this->assign('model', $model);
    }

    public function actionNewTitle(){
        $model = new ForumTitle();
        $model->section_id = $this->sectionId;
        if (isset($_POST['ForumTitle'])){
            $model->setAttributes($_POST['ForumTitle']);
            if ($model->save()){
                if (false != ($icon = $this->uploadImage("titles", 'icon', $model->id))){
                    if ($model->icon &&  $model->icon != "default.png"){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                    $model->icon = $icon;
                    if (!$model->save()){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                }
                Messages::get()->success("Title saved!");
                $this->goToAction('titles');
            }
        }
        $this->setPageLayout('title');
        $this->assign('model', $model);
    }

    public function actionEditTitle($id){
        $model = ForumTitle::findByPk($id);
        if ($model->section_id != $this->sectionId){
            Messages::get()->error("Access denied!");
            $this->goBack();
        }
        if (isset($_POST['ForumTitle'])){
            $model->setAttributes($_POST['ForumTitle']);
            if ($model->save()){
                if (false != ($icon = $this->uploadImage("titles", 'icon', $model->id))){
                    if ($model->icon &&  $model->icon != "default.png"){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                    $model->icon = $icon;
                    if (!$model->save()){
                        @unlink($this->getUploadFolder() . $model->icon);
                    }
                }
                Messages::get()->success("Title saved!");
                $this->goToAction('titles');
            }
        }
        $this->setPageLayout('title');
        $this->assign('model', $model);
    }


    public function actionDelete(){
        if (isset($_POST['ForumUserGroup'])){
            $models = ForumUserGroup::findAllByPk($_POST['ForumUserGroup']);
            foreach ($models as $model){
                $model->delete();
            }
            Messages::get()->success("Deleted!");
            $this->goBack();
        }
        if (isset($_POST['ForumCategory'])){
            $models = ForumCategory::findAllByPk($_POST['ForumCategory']);
            foreach ($models as $model){
                $model->delete();
            }
            Messages::get()->success("Deleted!");
            $this->goBack();
        }
        if (isset($_POST['ForumSubcategory'])){
            $models = ForumSubcategory::findAllByPk($_POST['ForumSubcategory']);
            foreach ($models as $model){
                $model->delete();
            }
            Messages::get()->success("Deleted!");
            $this->goBack();
        }
    }

}