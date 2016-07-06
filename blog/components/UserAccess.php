<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 06.07.2016
 * Time: 16:37
 */

namespace mpf\modules\blog\components;


use mpf\WebApp;

class UserAccess
{

    public static function canWrite()
    {
        return WebApp::get()->user()->isConnected();
    }
}