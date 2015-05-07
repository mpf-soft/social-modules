<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 12:55
 */

namespace mpf\modules\forum\models;

use app\components\htmltools\Messages;
use app\controllers\User;
use mpf\helpers\ArrayHelper;
use mpf\modules\forum\components\UserAccess;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

/**
 * Class ForumCategory
 * @package app\models
 * @property int $id
 * @property string $name
 * @property string $url_friendly_name
 * @property int $order
 * @property int $user_id
 * @property int $section_id
 * @property string $icon
 * @property \mpf\modules\forum\models\ForumSection $section
 * @property \mpf\modules\forum\models\ForumSubcategory[] $subcategories
 * @property \app\models\User $author
 */
class ForumCategory extends DbModel {

    public $groupRights = [];

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_categories";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'url_friendly_name' => 'Url Friendly Name',
            'order' => 'Order',
            'user_id' => 'Author',
            'section_id' => 'Section',
            'icon' => 'Icon'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'section' => [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumSection', 'section_id'],
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'subcategories' => [DbRelations::HAS_MANY, '\mpf\modules\forum\models\ForumSubcategory', 'category_id'],
            'groups' => [DbRelations::MANY_TO_MANY, '\mpf\modules\forum\models\ForumUserGroup', 'forum_groups2categories(category_id, group_id)']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return [
            ["id, name, url_friendly_name, order, user_id, section_id", "safe", "on" => "search"]
        ];
    }

    /**
     * @param int $sectionId
     * @param bool $forPublic
     * @return static[]
     */
    public static function findAllBySection($sectionId, $forPublic = false) {
        if (!$forPublic){
            return self::findAllByAttributes(['section_id' => $sectionId], ['order' => '`order` ASC']);
        }
        if (!UserAccess::get()->canRead($sectionId))
            return [];
        $condition = new ModelCondition(['model'=>__CLASS__]);
        $condition->join = "LEFT JOIN forum_groups2categories ON (category_id = id AND group_id = :group)";
        $condition->addCondition("canread IS NULL OR canread = 1");
        $condition->setParam(":group", UserAccess::get()->getUserGroup($sectionId, true));
        $condition->order = "`order` ASC";
        $condition->with = ['subcategories', 'subcategories.lastActiveThread', 'subcategories.lastActiveUser'];
        $condition->compareColumn("section_id", $sectionId);
        $categories =  self::findAll($condition);
        if (WebApp::get()->user()->isConnected()) {
            $subcategories = self::getDb()->table('forum_userhiddensubcategories')->where("user_id = :user")->setParam(":user", WebApp::get()->user()->id)->get();
            $subcategories = ArrayHelper::get()->transform($subcategories, "subcategory_id");
            foreach ($categories as $category) {
                foreach ($category->subcategories as $subcategory) {
                    if (in_array($subcategory->id, $subcategories)){
                        $subcategory->hidden = true;
                    }
                }
            }
        }
        return $categories;
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "name", "url_friendly_name", "order", "user_id", "section_id"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        $condition->with = ['author'];
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * @return array
     */
    public function getGroupFields() {
        $groups = ForumUserGroup::findAllByAttributes(['section_id' => $this->section_id]);
        $fields = [];
        foreach ($groups as $group) {
            $fields[] = [
                'name' => 'groupRights[' . $group->id . ']',
                'type' => 'select',
                'htmlOptions' => ['multiple' => 'multiple', 'style' => 'height:85px;'],
                'options' => [
                    'admin' => 'Admin',
                    'moderator' => 'Moderator',
                    'newthread' => 'Can Start a New Thread',
                    'threadreply' => 'Can Reply to an open Thread',
                    'canread' => 'Can View Threads'
                ],
                'label' => $group->full_name
            ];
        }
        return $fields;
    }

    public function beforeSave() {
        if (!UserAccess::get()->isSectionAdmin($this->section_id)) {
            Messages::get()->error("You can't edit this category!");
            return false;
        }
        if ($this->isNewRecord()) {
            $this->user_id = WebApp::get()->user()->id;
        }
        return parent::beforeSave();
    }

    public function beforeDelete() {
        if (!UserAccess::get()->isSectionAdmin($this->section_id)) {
            Messages::get()->error("You can't delete this category!");
            return false;
        }
        return parent::beforeDelete();
    }

    public function reloadGroupRights() {
        $rights = $this->getDb()->table('forum_groups2categories')->where("category_id = :category")->setParam(":category", $this->id)->get();
        $this->groupRights = [];
        foreach ($rights as $r) {
            $this->groupRights[$r['group_id']] = [];
            foreach (['admin', 'moderator', 'newthread', 'threadreply', 'canread'] as $column)
                if ($r[$column]) {
                    $this->groupRights[$r['group_id']][] = $column;
                }
        }
    }

    public function updateGroupRights() {
        $allGroups = ForumUserGroup::findAllBySection($this->section_id);
        foreach ($this->groupRights as $groupId => $details) {
            $this->getDb()->table('forum_groups2categories')->insert([
                'group_id' => $groupId,
                'category_id' => $this->id,
                'admin' => (int)in_array('admin', $details),
                'moderator' => (int)in_array('moderator', $details),
                'newthread' => (int)in_array('newthread', $details),
                'threadreply' => (int)in_array('threadreply', $details),
                'canread' => (int)in_array('canread', $details)
            ], [
                'admin' => (int)in_array('admin', $details),
                'moderator' => (int)in_array('moderator', $details),
                'newthread' => (int)in_array('newthread', $details),
                'threadreply' => (int)in_array('threadreply', $details),
                'canread' => (int)in_array('canread', $details)
            ]);
        }

        foreach ($allGroups as $grp){
            if (!isset($this->groupRights[$grp->id])){
                $this->getDb()->table('forum_groups2categories')->insert([
                    'group_id' => $grp->id,
                    'category_id' => $this->id,
                    'admin' => 0,
                    'moderator' => 0,
                    'newthread' => 0,
                    'threadreply' => 0,
                    'canread' => 0
                ], [
                    'admin' => 0,
                    'moderator' => 0,
                    'newthread' => 0,
                    'threadreply' => 0,
                    'canread' => 0
                ]);
            }
        }
    }
}
