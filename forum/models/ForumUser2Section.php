<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-23
 * Time: 12:50
 */

namespace mpf\modules\forum\models;

use mpf\modules\forum\components\Config;
use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\helpers\FileHelper;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\fields\Markdown;

/**
 * Class ForumUser2Section
 * @package app\modules\forum
 * @property int $id
 * @property int $user_id
 * @property int $section_id
 * @property int $muted
 * @property int $banned
 * @property int $title_id
 * @property int $group_id
 * @property string $member_since
 * @property string $signature
 * @property \app\models\User $user
 * @property \mpf\modules\forum\models\ForumSection $section
 * @property \mpf\modules\forum\models\ForumUserGroup $group
 * @property \mpf\modules\forum\models\ForumTitle $title
 */
class ForumUser2Section extends DbModel {

    public $name;
    public $last_login;

    public $icon;

    /**
     * A callable method that will be used when a new icon is loaded.
     * Param that will be sent:
     *      - iconKey ( used by $_FILES )
     *
     * If it's not set then "iconColumnName" will be used to save date for user;
     * @var callable
     */
    public $iconUploadHandle;

    public function getIconLocationURL(){
        return str_replace([
            '{WEB_ROOT}'
        ],[
            WebApp::get()->request()->getWebRoot()
        ], Config::value('USER_ICON_FOLDER_URL'));
    }

    public function getIconLocationPath(){
        return str_replace([
            '{APP_ROOT}',
            '{DIRECTORY_SEPARATOR}'
        ], [
            APP_ROOT,
            DIRECTORY_SEPARATOR
        ], Config::value('USER_ICON_FOLDER_PATH'));
    }

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_users2sections";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
            'id' => 'Id',
            'user_id' => 'User',
            'section_id' => 'Section',
            'muted' => 'Muted',
            'banned' => 'Banned',
            'title_id' => 'Title',
            'group_id' => 'Group',
            'member_since' => 'Member Since',
            'signature' => 'Signature'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'user' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'section' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumSection', 'section_id'],
            'group' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumUserGroup', 'group_id'],
            'title' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumTitle', 'title_id']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, user_id, section_id, muted, banned, title_id, group_id, member_since, signature", "safe", "on" => "search"]
        ];
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider($sectionId) {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->with = ['user', 'group', 'title'];
        $condition->compareColumn("section_id", $sectionId);
        foreach (["id", "section_id", "muted", "banned", "title_id", "group_id", "member_since", "signature"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        if ($this->user_id && !is_numeric($this->user_id)) {
            $condition->compareColumn('user.name', $this->user_id);
        } elseif ($this->user_id) {
            $condition->compareColumn('user_id', $this->user_id);
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * Makes a new user member of a selected section with a selected group.
     * @param $userId
     * @param $sectionId
     * @param $groupId
     * @return bool
     */
    public static function makeMember($userId, $sectionId = 0, $groupId = null) {
        $user = new self();
        $user->user_id = $userId;
        $user->section_id = $sectionId;
        if (!$groupId) {
            $section = ForumSection::findByPk($sectionId);
            $groupId = $section->default_members_group_id;
        }
        App::get()->debug("User $userId assign to group $groupId from section $sectionId");
        $user->group_id = $groupId;
        return $user->save();
    }

    /**
     * @return string
     */
    public function getSignature() {
        if (!$this->signature){
            return "";
        }
        if (Config::value("FORUM_TEXT_PARSER_CALLBACK") && is_callable(Config::value("FORUM_TEXT_PARSER_CALLBACK"))){
            $text = call_user_func(Config::value("FORUM_TEXT_PARSER_CALLBACK"), $this->signature);
        } else {
            $text = Markdown::processText(htmlentities($this->signature));
        }
        return $text . Html::get()->scriptFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/highlight.pack.js') .
        Html::get()->cssFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/styles/github.css') .
        Html::get()->script('hljs.tabReplace = \'    \';hljs.initHighlightingOnLoad();');

    }

    public function getProfileLink() {
        if ($this->section_id) {
            return Html::get()->link(['user', 'index', ['id' => $this->id, 'name' => $this->user->name, WebApp::get()->getController()->sectionIdKey => $this->section_id]], $this->user->name);
        } else {
            return Html::get()->link(['user', 'index', ['id' => $this->id, 'name' => $this->user->name]], $this->user->name);
        }
    }

    public function changeIcon(){
        if (!isset($_FILES['icon']) || !$_FILES['icon']['tmp_name']){
            return null;
        }
        if ($this->iconUploadHandle){
            $function = $this->iconUploadHandle;
            return $function('icon');
        }
        $name = $this->user_id . substr($_FILES['icon']['name'], -30);
        FileHelper::get()->upload('icon', $this->getIconLocationPath() . $name);
        $column = Config::value('USER_ICON_COLUMN_NAME');
        $old = $this->user->$column;
        if ($old && 'default.png' != $old && $name != $old){
            @unlink($old);
        }
        $this->user->$column = $name;
        $this->user->save();
    }
}
