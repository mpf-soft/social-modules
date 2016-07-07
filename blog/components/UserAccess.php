<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 06.07.2016
 * Time: 16:37
 */

namespace mpf\modules\blog\components;


use mpf\base\LogAwareSingleton;
use mpf\WebApp;

class UserAccess extends LogAwareSingleton
{

    /**
     * Can be defined to a custom function that will return true/false if current user can write an article or not
     * @var callable
     */
    public $canWriteCallback;

    /**
     * Can be defined to a custom function that will return true/false if current user can edit categories
     * @var callable
     */
    public $canEditCategoriesCallback;

    /**
     * Can be defined to a custom function that will return true/false if current user can write comments
     * @var callable
     */
    public $canPostCommentsCallback;

    /**
     * Can be defined to a custom function that will return true/false if current user can moderate comments
     * @var callable
     */
    public $canModerateCommentsCallback;

    /**
     * Can be defined to a custom function that will return true/false if current user can edit other authors articles
     * @var callable
     */
    public $canEditOtherArticlesCallback;

    /**
     * @var bool
     */
    public $allowCommentsAsGuest = true;

    /**
     * Check if current user can post comments. Must manually check if current post allows comments;
     * @return bool
     */
    public static function canComment()
    {
        if (!WebApp::get()->user()->isConnected()) {
            return self::get()->allowCommentsAsGuest;
        }
        if (is_callable(self::get()->canPostCommentsCallback)) {
            return call_user_func(self::get()->canPostCommentsCallback);
        }
        return true;
    }

    /**
     * Check if current user can moderate comments( edit / delete )
     * @return bool
     */
    public static function canModerate()
    {
        if (WebApp::get()->user()->isGuest())
            return false;

        if (is_callable(self::get()->canModerateCommentsCallback)) {
            return call_user_func(self::get()->canModerateCommentsCallback);
        }

        return false;
    }

    /**
     * Check if current user can write articles
     * @return bool
     */
    public static function canWrite()
    {
        if (WebApp::get()->user()->isGuest())
            return false;

        if (is_callable(self::get()->canWriteCallback)) {
            return call_user_func(self::get()->canWriteCallback);
        }

        return false;
    }

    /**
     * Check if current user can edit categories
     * @return bool
     */
    public static function canEditCategories()
    {
        if (WebApp::get()->user()->isGuest())
            return false;

        if (is_callable(self::get()->canEditCategoriesCallback)) {
            return call_user_func(self::get()->canEditCategoriesCallback);
        }

        return false;
    }

    /**
     * Check if current users can edit articles that  are not his/her own
     * @return bool
     */
    public static function canEditOtherArticles()
    {
        if (WebApp::get()->user()->isGuest())
            return false;

        if (is_callable(self::get()->canEditOtherArticlesCallback)) {
            return call_user_func(self::get()->canEditOtherArticlesCallback);
        }

        return false;
    }
}