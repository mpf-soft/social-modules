<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:16
 */

namespace app\modules\forum\components;


use app\components\htmltools\Messages;
use app\modules\forum\models\ForumSection;
use mpf\helpers\FileHelper;
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
     * Will record the ID of the current section;
     * @var int
     */
    public $sectionId = 0;

    /**
     * Key to be used when generating links where section is needed (cp links for section admins + home of the forum)
     * @var string
     */
    public $sectionIdKey = 'section';

    /**
     * Section ID will be sent in links only if source is set to "get". It will also automatically read section id
     * if source has one of the following values: get, post, session. If not it will let the user to manage the section ID
     * and to update it to controller.
     * @var string
     */
    public $sectionIdSource = 'get';

    /**
     * Change this for special aliases.
     * @var string
     */
    public $forumModuleAlias = 'forum';

    /**
     * Folder location where uploads for categories icons can be uploaded
     * @var string
     */
    public $uploadLocation = '{APP_ROOT}../htdocs/uploads{DIRECTORY_SEPARATOR}forum{DIRECTORY_SEPARATOR}';

    /**
     * Public URL for upload location
     * @var string
     */
    public $uploadURL = '{WEB_ROOT}uploads/forum/';

    /**
     * Used to separate subpages in title.
     * @var string
     */
    public $pageTitleSeparator = "-";

    public function getUploadFolder(){
        $moduleFolder = $this->getRequest()->getModulePath();
        $controllerFolder = $this->request->getController();
        return str_replace(['{APP_ROOT}', '{MODULE_FOLDER}', '{CONTROLLER}', '{LIBS_FOLDER}', '{DIRECTORY_SEPARATOR}'],
            [APP_ROOT, $moduleFolder, $controllerFolder, LIBS_FOLDER, DIRECTORY_SEPARATOR], $this->uploadLocation);
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
                $fname = $_FILES[$name]['name'];
                if (strlen($fname) > 100){
                    $fname = trim(substr($fname, -100));
                }
                $finalName .= $fname;
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
        $moduleFolder = $this->getRequest()->getModulePath();
        $controllerFolder = $this->request->getController();
        return str_replace(['{WEB_ROOT}', '{LINK_ROOT}', '{MODULE_FOLDER}', '{CONTROLLER}', '{LIBS_FOLDER}'],
            [$this->getWebRoot(), $this->getLinkRoot(), $moduleFolder, $controllerFolder, LIBS_FOLDER], $this->uploadURL);
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
        switch ($this->sectionIdSource) {
            case 'get':
                if (isset($_GET[$this->sectionIdKey]))
                    $this->sectionId = $_GET[$this->sectionIdKey];
                break;
            case 'post':
                if (isset($_POST[$this->sectionIdKey]))
                    $this->sectionId = $_POST[$this->sectionIdKey];
                break;
            case 'session':
                if (Session::get()->exists($this->sectionIdKey))
                    $this->sectionId = Session::get()->value($this->sectionIdKey);
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
        if ('get' != $this->sectionIdSource)
            return $original;
        if (isset($original[2]) && is_array($original[2])) {
            $original[2][$this->sectionIdKey] = $this->sectionId;
        } elseif (isset($original[2])) {
            $original[3] = $original[2];
            $original[2] = [$this->sectionIdKey => $this->sectionId];
        } else {
            $original[2] = [$this->sectionIdKey => $this->sectionId];
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
        if ($this->sectionId && 'get' == $this->sectionIdSource)
            $params['section'] = $this->sectionId;
        return parent::goToPage($controller, $action, $params);
    }

    /**
     * Updates the link when changing action to also add section id in case that is not the default one
     * @param string $action
     * @param array $params
     * @return bool
     */
    public function goToAction($action, $params = []){
        if ($this->sectionId && 'get' == $this->sectionIdSource)
            $params['section'] = $this->sectionId;
        return parent::goToAction($action, $params);
    }

    public function getUrlForDatatableAction($action, $params = [], $controller = null, $key = 'id', $column = 'id'){
        $controller = is_null($controller)?"\\mpf\\WebApp::get()->request()->getController()":"'$controller'";
        if ($this->sectionId&& 'get' == $this->sectionIdSource){
            $params[$this->sectionIdKey] = $this->sectionId;
        }
        $prms = ["\"$key\" => \$row->{$column}"];
        foreach ($params as $name=>$value){
            $prms[] = "\"$name\" => '$value'";
        }
        $prms = implode(", ", $prms);
        return "\\mpf\\WebApp::get()->request()->createURL($controller, '$action', [$prms])";
    }

}