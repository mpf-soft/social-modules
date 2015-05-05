<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 14:48
 */

namespace mpf\modules\forum\controllers;

use mpf\modules\forum\components\Controller;
use mpf\modules\forum\models\ForumCategory;
use mpf\WebApp;

class Home extends Controller{

    public function actionIndex(){
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionId, true));
    }

    public function actionAction(){
        if (!isset($_POST['action'])){
            $this->goToPage('special', 'notFound');
        }
        switch ($_POST['action']){
            case "show_subcategory" :
                WebApp::get()->sql()->table("forum_userhiddensubcategories")->where("user_id = :user AND subcategory_id = :subcategory")
                        ->setParams([':user' => WebApp::get()->user()->id, ':subcategory'=> $_POST['id']])
                        ->delete();
                $this->goBack();
                break;
            case "hide_subcategory" :
                WebApp::get()->sql()->table("forum_userhiddensubcategories")->insert([
                    'user_id' => WebApp::get()->user()->id,
                    'subcategory_id' => $_POST['id']
                ], "ignore");
                $this->goBack();
                break;
            default:
                $this->goToPage('special', 'notFound');
        }
    }
}