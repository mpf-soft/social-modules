<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.03.2015
 * Time: 11:06
 */

namespace app\modules\forum\controllers;


use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumUser2Section;
use mpf\WebApp;

class User extends Controller{

    public function actionIndex($id){

    }

    public function actionControlPanel(){
        $user  = ForumUser2Section::findByAttributes(['user_id' => WebApp::get()->user()->id, 'section_id' => $this->sectionId]);
        $user->icon = $user->user->icon;
        if (isset($_POST['ForumUser2Section'])){
            $user->signature = $_POST['ForumUser2Section']['signature'];
            $user->save();
            $user->changeIcon();
            $user->icon = $user->user->icon;
            WebApp::get()->user()->setState('icon', $user->user->icon);
        }
        $this->assign('model', $user);
    }
}