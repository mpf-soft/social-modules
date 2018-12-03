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
use mpf\base\Singleton;
use mpf\web\helpers\Html;
use mpf\WebApp;

class Config extends Singleton
{

    public static function value($key)
    {
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

    /**
     * A function that will return a string with url to user profile. For custom pages.
     *
     * Parameters sent:
     *    - userId -> site user ID
     *    - userName -> user Name
     * Example:
     *
     * [php]
     *   function($userId, $userName){
     *      return \mpf\WebApp::get()->request()->createURL('profile', 'index', ['id' => $userId, 'name' => $userName], '');
     *   }
     * [/php]
     * @var callable
     */
    public $userProfileLinkCallback;

    /**
     * Get HTML link to user profile
     * @param $userId
     * @param $userName
     * @param array $htmlOptions
     * @return string
     */
    public function getProfileLink($userId, $userName, $htmlOptions = [])
    {
        if (isset($this->userProfileLinkCallback) && is_callable($this->userProfileLinkCallback)) {
            return Html::get()->link(call_user_func($this->userProfileLinkCallback, $userId, $userName), $userName, $htmlOptions);
        }

        return Html::get()->link(WebApp::get()->getController()->updateURLWithSection(['user', 'index', ['id' => $userId, 'name' => $userName]]), $userName, $htmlOptions);
    }

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
    public $FORUM_REPLIES_PER_PAGE = 20;

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
    public $FORUM_MAX_REPLY_LEVELS = 16;

    /**
     * If set to true then on user control panel there is an option to change icon
     * @var bool
     */
    public $FORUM_HANDLE_USER_ICON = true;

    /**
     * URL to vote up icon.
     * @var string
     */
    public $FORUM_VOTE_AGREE_ICON = '{WEB_ROOT}forum/vote-icons/opinion-agree.png';

    /**
     * URL to vote down icon.
     * @var string
     */
    public $FORUM_VOTE_DISAGREE_ICON = '{WEB_ROOT}forum/vote-icons/opinion-disagree.png';

    /**
     * Prefix to be added to subscription names;
     * @var string
     */
    public $FORUM_NOTIFICATIONS_SUBSCRIPTIONS_PREFIX = 'MPF.SocialModules.Forum.';

    /**
     * Prefix to be added to type names;
     * @var string
     */
    public $FORUM_NOTIFICATIONS_TYPES_PREFIX = 'MPF.SocialModules.Forum.';

    /**
     * If you need to parse the text before displaying then set a parser here that will be used instead of the default one
     * @var callback
     */
    public $FORUM_TEXT_PARSER_CALLBACK;

    /**
     * Name of the class used as input. Default is Markdown
     * @var string
     */
    public $FORUM_REPLY_INPUT_TYPE = 'Markdown';

}