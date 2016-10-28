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
    public $languages;

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

    /**
     * URL location for user avatars;
     * @var string
     */
    public $userAvatarURL = 'uploads/user-avatars/';

    public $postsPerPage = 5;

    public $introductionSeparator = "==END-INTRODUCTION==";


    /**
     * @var bool
     */
    public $allowComments = true;

    /**
     * @return string
     */
    public function getActiveLanguage()
    {
        if (is_null($this->languages))
            $this->languages = WebApp::get()->request()->getAvailableLanguages();
        if (1 === count($this->languages))
            return $this->languages[0];

        $l = WebApp::get()->request()->getLanguage();
        if (!in_array($l, $this->languages))
            return $this->languages[0];
        return $l;
    }

}