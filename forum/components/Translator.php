<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 16.03.2015
 * Time: 11:50
 */

namespace app\modules\forum\components;


use mpf\base\TranslatableObject;

class Translator extends TranslatableObject{
    /**
     * @var Translator
     */
    private static $self;

    /**
     * @return Translator
     */
    public static function get(){
        if (!self::$self){
            self::$self = new self();
        }
        return self::$self;
    }
}