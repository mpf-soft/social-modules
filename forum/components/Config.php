<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 31.03.2015
 * Time: 19:58
 */

namespace mpf\modules\forum\components;


use app\models\GlobalConfig;
use mpf\base\App;
use mpf\base\Object;
use mpf\WebApp;

class Config extends Object {

    /**
     * @var static
     */
    protected static $self;

    /**
     * @return static
     */
    public static function get() {
        if (!self::$self) {
            self::$self = new static();
        }
        return self::$self;
    }

    public static function value($key) {
        if (self::get()->useGlobalConfig) {
            $value = GlobalConfig::value($key) ?: self::get()->$key;
        } else {
            $value = self::get()->$key;
        }
        $value = str_replace(['{APP_ROOT}', '{DIRECTORY_SEPARATOR}'], [APP_ROOT, DIRECTORY_SEPARATOR], $value);
        if (is_a(App::get(), WebApp::className())) {
            return str_replace(['{WEB_ROOT}', '{MODULE_FOLDER}'], [WebApp::get()->request()->getWebRoot(), WebApp::get()->request()->getModulePath()], $value);
        }
        return $value;

    }

    /**
     * If set to true it will use global config to get values. If not it will use this class attributes.
     * It will also use this class attributes in case that global config is not found.
     * @var bool
     */
    public $useGlobalConfig = false;

    public $USER_ICON_FOLDER_PATH = '{APP_ROOT}..{DIRECTORY_SEPARATOR}htdocs{DIRECTORY_SEPARATOR}uploads{DIRECTORY_SEPARATOR}user-avatars{DIRECTORY_SEPARATOR}';

    public $USER_ICON_FOLDER_URL = '{WEB_ROOT}uploads/user-avatars/';

    public $USER_ICON_COLUMN_NAME = 'icon';
    /**
     * Key to be used when generating links where section is needed (cp links for section admins + home of the forum)
     * @var string
     */
    public $FORUM_SECTION_ID_KEY = 'section';
    /**
     * Section ID will be sent in links only if source is set to "get". It will also automatically read section id
     * if source has one of the following values: get, post, session. If not it will let the user to manage the section ID
     * and to update it to controller.
     * @var string
     */
    public $FORUM_SECTION_ID_SOURCE = 'get';
    /**
     * Change this for special aliases.
     * @var string
     */
    public $FORUM_MODULE_ALIAS = 'forum';
    /**
     * Folder location where uploads for categories icons can be uploaded
     * @var string
     */
    public $FORUM_UPLOAD_LOCATION = '{APP_ROOT}..{DIRECTORY_SEPARATOR}htdocs{DIRECTORY_SEPARATOR}uploads{DIRECTORY_SEPARATOR}forum{DIRECTORY_SEPARATOR}';
    /**
     * Public URL for upload location
     * @var string
     */
    public $FORUM_UPLOAD_URL = '{WEB_ROOT}uploads/forum/';
    /**
     * Used to separate subpages in title.
     * @var string
     */
    public $FORUM_PAGE_TITLE_SEPARATOR = " &#187; ";

    /**
     * Number of threads to display per page on subcategory page.
     * @var int
     */
    public $FORUM_THREADS_PER_PAGE = 15;

    /**
     * Number of replies to display per page
     * @var int
     */
    public $FORUM_REPLIES_PER_PAGE = 10;

    /**
     * A string that is used to separate message from signature
     * @var string
     */
    public $FORUM_THREAD_SIGNATURE_SEPARATOR = '<br /><br />';

    /**
     * Max level of replies for forum. Forum framework supports a high number of replies but you may want to limit then for design
     * purposes.
     * @var int
     */
    public $FORUM_MAX_REPLY_LEVELS = 8;

}