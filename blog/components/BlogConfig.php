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

    /**
     * Number of posts that is displayed per page
     * @var int
     */
    public $postsPerPage = 5;

    /**
     * This will be used to extract the short description
     * @var string
     */
    public $introductionSeparator = "==END-INTRODUCTION==";

    /**
     * A list of visibilies IDs and Labels that are possible for articles.
     *
     * Example:
     * [php][
     *     0 => 'Members (Logged In Only)',
     *     1 => 'Public (Everyone)'
     * ];
     * [/php]
     * @var string[]
     */
    public $visibilityOptions = [
        0 => 'Members (Logged In Only)',
        1 => 'Public (Everyone)'
    ];

    /**
     * A function that should return a list of visibility options that are available for current user.
     * @var callable
     */
    public $myVisibiliesCallback;

    /**
     *
     * Allow comments on articles
     * @var bool
     */
    public $allowComments = true;


    /**
     * If multiple blog modules are used then a table prefix can be used to manage all of them separately
     * @var string
     */
    public $tablesPrefix = 'blog_';

    protected function init($config)
    {
        if (is_null($this->languages))
            $this->languages = WebApp::get()->request()->getAvailableLanguages();
        parent::init($config);
    }

    /**
     * @return string
     */
    public function getActiveLanguage()
    {
        if (1 === count($this->languages))
            return $this->languages[0];

        $l = WebApp::get()->request()->getLanguage();
        if (!in_array($l, $this->languages))
            return $this->languages[0];
        return $l;
    }

}