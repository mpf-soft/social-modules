<?php
/**
 * Created by MPF Framework.
 * Date: 2015-03-13
 * Time: 12:55
 */

namespace app\modules\forum\models;

use app\components\htmltools\Messages;
use app\modules\forum\components\UserAccess;
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
 * @property \app\modules\forum\models\ForumSection $section
 * @property \app\modules\forum\models\ForumSubcategory[] $subcategories
 * @property \app\models\User $owner
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
            'user_id' => 'Owner',
            'section_id' => 'Section'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return [
            'section' => [DbRelations::BELONGS_TO, '\app\modules\forum\models\ForumSection', 'section_id'],
            'owner' => [DbRelations::BELONGS_TO, '\app\models\User', 'user_id'],
            'subcategories' => [DbRelations::HAS_MANY, '\app\modules\forum\models\ForumSubcategory', 'category_id']
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
    public static function findAllBySection($sectionId, $forPublic = false){
        return self::findAllByAttributes(['section_id' => $sectionId]);
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
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    /**
     * @return array
     */
    public function getGroupFields(){
        $groups = ForumUserGroup::findAllByAttributes(['section_id' => $this->section_id]);
        $fields = [];
        foreach ($groups as $group){
            $fields[] = [
                'name' => 'groupRights[' . $group->id . ']',
                'type' => 'select',
                'fieldHtmlOptions' => ['multiple' => 'multiple'],
                'options' => [
                    'admin'=>'Admin',
                    'moderator' => 'Moderator',
                    'newthread' => 'Can Start a New Thread',
                    'threadreply' => 'Can Reply to an open Thread',
                    'canread' => 'Can View Threads'
                ]
            ];
        }
        return $fields;
    }

    public function beforeSave(){
        if (!UserAccess::get()->isSectionAdmin($this->section_id)){
            Messages::get()->error("You can't edit this category!");
            return false;
        }
        if ($this->isNewRecord()){
            $this->user_id = WebApp::get()->user()->id;
        }
        return parent::beforeSave();
    }

    public function beforeDelete(){
        if (!UserAccess::get()->isSectionAdmin($this->section_id)){
            Messages::get()->error("You can't delete this category!");
            return false;
        }
        return parent::beforeDelete();
    }
}
