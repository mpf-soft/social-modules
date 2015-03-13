<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:16
 */

namespace app\modules\forum\components;


class Controller extends \app\components\Controller{
    /**
     * Change this when extending forum to other sections (like a forum for each group of users in a social network site)
     * @var int
     */
    public $sectionID = 0;

    /**
     * In case that there will be changes for views folders then a new path can be set here.
     * @var string
     */
    public $viewsPath = 'default';
}