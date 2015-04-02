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
        'ForumUserGroup' => '\mpf\modules\forum\models\ForumUserGroup',
        'User' => '\app\models\User',
        'GlobalConfig' => '\app\models\GlobalConfig'
    ];

    protected static $_self;

    public static function get(){
        if (!self::$_self)
            self::$_self = new static();
        return self::$_self;
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($method, $params = []){
        $model = self::get()->models[$params[0]];
        unset($params[0]);
        return call_user_func_array($model.'::'.$method, array_values($params));
    }

    /**
     * @param $model
     * @return \mpf\datasources\sql\DbModel
     */
    public static function model($model){
        $model = self::get()->models[$model];
        return $model::model();
    }

    /**
     * @param string $model
     * @param string|int $pk
     * @return \mpf\datasources\sql\DbModel
     */
    public static function findByPk($model, $pk){
        $model = self::get()->models[$model];
        return $model::findByPk($pk);
    }

    /**
     * @param string $model
     * @param string[] $attributes
     * @return \mpf\datasources\sql\DbModel
     */
    public static function findByAttributes($model, $attributes){
        $model = self::get()->models[$model];
        return $model::findByAttributes($attributes);
    }

    /**
     * @param $model
     * @return \mpf\datasources\sql\DbModel
     */
    public static function getNew($model){
        $model = self::get()->models[$model];
        return new $model();
    }

}