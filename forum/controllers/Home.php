<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 14:48
 */

namespace app\modules\forum\controllers;

use app\modules\forum\components\Controller;
use app\modules\forum\models\ForumCategory;

class Home extends Controller{

    /**
     * Change this when extending forum to other sections (like a forum for each group of users in a social network site)
     * @var int
     */
    public $sectionID = 0;


    public function actionIndex(){
        $this->assign('categories', ForumCategory::findAllBySection($this->sectionID));
    }
}