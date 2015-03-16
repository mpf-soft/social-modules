<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.03.2015
 * Time: 15:16
 */

namespace app\modules\forum\components;


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

}