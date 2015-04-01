<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 14:51
 */
namespace mpf\modules\forum\components;

use mpf\base\Object;

/**
 * Class ModelHelper
 * Allows developer to extend the current models used  by forum module.
 * There is no option here for controllers as they can easily be extended using controllerAliases option from WebApp class config.
 * Example for controller in config:
 *  $config['WebApp']['controllerAliases'] = [
 *      'forum/user' => '\app\forum-extenders\controllers\User'
 *  ];
 * @package mpf\modules\forum\components
 */
class ModelHelper extends Object{
    public $models = [
        'ForumCategory' => '\mpf\modules\forum\models\ForumCategory',
        'ForumReply' => '\mpf\modules\forum\models\ForumReply',
        'ForumSection' => '\mpf\modules\forum\models\ForumSection',
        'ForumSubcategory' => '\mpf\modules\forum\models\ForumSubcategory',
        'ForumThread' => '\mpf\modules\forum\models\ForumThread',
        'ForumTitle' => '\mpf\modules\forum\models\ForumTitle',
        'ForumUser2Section' => '\mpf\modules\forum\models\ForumUser2Section',
        'ForumUserGroup' => '\mpf\modules\forum\models\ForumUserGroup'
    ];
}