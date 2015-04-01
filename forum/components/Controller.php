<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:16
 */

namespace mpf\modules\forum\components;


use app\components\htmltools\Messages;
use mpf\modules\forum\models\ForumSection;
use mpf\helpers\FileHelper;
use mpf\web\helpers\Html;
use mpf\web\Session;

class Controller extends \app\components\Controller {

    /**
     * A folder with general components that can be changed to use custom templates.
     * It contains stuff like top user panel, user left panel from each post
     * @var string
     */
    public $visibleComponentsFolder = '{MODULE_FOLDER}views{DIRECTORY_SEPARATOR}components{DIRECTORY_SEPARATOR}';

    /**
     * It will be use as second class name for the div that contains the entire forum pages.
     * @var string
     */
    public $forumPageTheme = 'basic-forum-page';

    /**
     * Path to current layout
     * @var string
     */
    public $layoutFolder = '{APP_ROOT}views{DIRECTORY_SEPARATOR}layout';

    /**
     * Will record the ID of the current section;
     * @var int
     */
    public $sectionId = 0;

    public function getUploadFolder(){
        return Config::value('FORUM_UPLOAD_LOCATION');
    }

    /**
     * Upload image
     * @param $for
     * @param $name
     * @param $id
     * @return bool|string
     */
    public function uploadImage($for, $name, $id){
        $folder = $this->getUploadFolder() . $for . DIRECTORY_SEPARATOR . $id . '-';
        $finalName = "$id-";
        if (isset($_FILES[$name]) && file_exists($_FILES[$name]['tmp_name'])){
            if (FileHelper::get()->isImage($_FILES[$name]['tmp_name'])){
                $finalName .= ($fname = trim(substr($_FILES[$name]['name'], -100)));
                if (FileHelper::get()->upload($name, $folder . $fname)) {
                    return $finalName;
                }
                return false;
            } else {
                Messages::get()->error("Selected file isn't a image!");
                return false;
            }
        }
        return false;
    }

    public function getUploadUrl(){
        return Config::value('FORUM_UPLOAD_URL');
    }

    /**
     * Display single view component. It will automatically  prepend folder location and append file extension.
     * @param string $name
     * @param array $params
     */
    public function displayComponent($name, $params = []) {
        $moduleFolder = $this->getRequest()->getModulePath();
        $controllerFolder = $this->request->getController();
        $folder = str_replace(['{APP_ROOT}', '{MODULE_FOLDER}', '{CONTROLLER}', '{LIBS_FOLDER}', '{DIRECTORY_SEPARATOR}'],
            [APP_ROOT, $moduleFolder, $controllerFolder, LIBS_FOLDER, DIRECTORY_SEPARATOR], $this->visibleComponentsFolder);
        $this->display($folder . $name . '.php', $params);
    }

    public function beforeAction($actionName) {
        $key = Config::value('FORUM_SECTION_ID_KEY');
        switch (Config::value('FORUM_SECTION_ID_SOURCE')) {
            case 'get':
                if (isset($_GET[$key]))
                    $this->sectionId = $_GET[$key];
                break;
            case 'post':
                if (isset($_POST[$key]))
                    $this->sectionId = $_POST[$key];
                break;
            case 'session':
                if (Session::get()->exists($key))
                    $this->sectionId = Session::get()->value($key);
                break;
        }
        if (!in_array($actionName, ['notFound', 'accesDenied'])) {
            $section = ForumSection::findByPk($this->sectionId);
            if (!$section) {
                $this->goToPage('special', 'notFound');
            }
        }
        return parent::beforeAction($actionName);
    }

    /**
     * Adds section id in links that need it
     * @param $original
     * @return mixed
     */
    public function updateURLWithSection($original) {
        if (!$this->sectionId)
            return $original;
        if ('get' != Config::value('FORUM_SECTION_ID_SOURCE'))
            return $original;
        if (isset($original[2]) && is_array($original[2])) {
            $original[2][Config::value('FORUM_SECTION_ID_KEY')] = $this->sectionId;
        } elseif (isset($original[2])) {
            $original[3] = $original[2];
            $original[2] = [Config::value('FORUM_SECTION_ID_KEY') => $this->sectionId];
        } else {
            $original[2] = [Config::value('FORUM_SECTION_ID_KEY') => $this->sectionId];
        }

        return $original;
    }

    /**
     * Updates the link when changing page to also add section id in case that is not the default one
     * @param string $controller
     * @param null|string $action
     * @param array $params
     * @return bool|void
     */
    public function goToPage($controller, $action = null, $params = []) {
        if ($this->sectionId && 'get' == Config::value('FORUM_SECTION_ID_SOURCE'))
            $params[Config::value('FORUM_SECTION_ID_KEY')] = $this->sectionId;
        return parent::goToPage($controller, $action, $params);
    }

    /**
     * Updates the link when changing action to also add section id in case that is not the default one
     * @param string $action
     * @param array $params
     * @return bool
     */
    public function goToAction($action, $params = []){
        if ($this->sectionId && 'get' == Config::value('FORUM_SECTION_ID_SOURCE'))
            $params[Config::value('FORUM_SECTION_ID_KEY')] = $this->sectionId;
        return parent::goToAction($action, $params);
    }

    public function getUrlForDatatableAction($action, $params = [], $controller = null, $key = 'id', $column = 'id'){
        $controller = is_null($controller)?"\\mpf\\WebApp::get()->request()->getController()":"'$controller'";
        if ($this->sectionId&& 'get' == Config::value('FORUM_SECTION_ID_SOURCE')){
            $params[Config::value('FORUM_SECTION_ID_KEY')] = $this->sectionId;
        }
        $prms = ["\"$key\" => \$row->{$column}"];
        foreach ($params as $name=>$value){
            $prms[] = "\"$name\" => '$value'";
        }
        $prms = implode(", ", $prms);
        return "\\mpf\\WebApp::get()->request()->createURL($controller, '$action', [$prms])";
    }

    /**
     * @param int $page
     * @param string $label
     * @return string
     */
    public function getPageLink($page, $label){
        return Html::get()->link($this->getPageURL($page), $label);
    }

    /**
     * @param $page
     * @return string
     */
    public function getPageURL($page){

        return "#";
    }
}