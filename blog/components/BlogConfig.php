<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 07.07.2016
 * Time: 09:31
 */

namespace mpf\modules\blog\components;


use mpf\base\Singleton;
use mpf\WebApp;

class BlogConfig extends Singleton
{

    /**
     * To define a custom header for the blog full path to file can be specified here
     * @var string
     */
    public $customHeader;

    /**
     * To define a custom footer for the blog full path to file can be specified here
     * @var string
     */
    public $customFooter;

    /**
     * @var bool
     */
    public $showSideSearch = true;

    /**
     * @var bool
     */
    public $showSideCategories = true;

    /**
     * @var string[]
     */
    public $languages = ['en'];

    /**
     * Structure:
     * [
     *  [
     *      'title' => '<Custom Title Here>',
     *      'content' => <callback (  return string ) >
     *  ]
     * ]
     * @var array
     */
    public $customSidePanels = [];

    /**
     * @var string
     */
    public $articleImageURL = 'uploads/blog/';

    /**
     * @var string
     */
    public $articleImageLocation = 'uploads/blog/';

    public $postsPerPage = 5;

    public $introductionSeparator = "==END-INTRODUCTION==";

    /**
     * @return string
     */
    public function getActiveLanguage()
    {
        return WebApp::get()->request()->getLanguage();
    }

}