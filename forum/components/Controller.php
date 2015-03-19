<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:16
 */

namespace app\modules\forum\components;


use mpf\web\Session;

class Controller extends \app\components\Controller{

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
     * Display single view component. It will automatically  prepend folder location and append file extension.
     * @param string $name
     * @param array $params
     */
    public function displayComponent($name, $params = []){
        $moduleFolder = $this->getRequest()->getModulePath();
        $controllerFolder = $this->request->getController();
        $folder = str_replace(['{APP_ROOT}', '{MODULE_FOLDER}', '{CONTROLLER}', '{LIBS_FOLDER}', '{DIRECTORY_SEPARATOR}'],
            [APP_ROOT, $moduleFolder, $controllerFolder, LIBS_FOLDER, DIRECTORY_SEPARATOR], $this->visibleComponentsFolder);
        $this->display($folder . $name . '.php', $params);
    }

    public function beforeAction($actionName){
        switch ($this->sectionIdSource){
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
        return parent::beforeAction($actionName);
    }

}