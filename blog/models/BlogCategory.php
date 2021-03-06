<?php
/**
 * Created by MPF Framework.
 * Date: 2016-07-07
 * Time: 08:39
 */

namespace mpf\modules\blog\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\modules\blog\components\BlogConfig;
use mpf\WebApp;

/**
 * Class BlogCategory
 * @package app\models
 * @property int $id
 * @property string $name
 * @property int $added_by
 * @property string $added_time
 * @property string
 * @property \app\models\User $user
 */
class BlogCategory extends DbModel
{

    public $title = [], $description = [];

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return BlogConfig::get()->tablesPrefix . "categories";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels()
    {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'added_by' => 'Added By',
            'added_time' => 'Added Time'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'user' => [DbRelations::BELONGS_TO, '\app\models\User', 'added_by'],
        ];
    }

    public static function getFormFields()
    {
        $fields = ['name'];
        foreach (BlogConfig::get()->languages as $lang) {
            $fields[] = 'title[' . $lang . ']';
            $fields[] = 'description[' . $lang . ']';
        }
        return $fields;
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, name, added_by, added_time", "safe", "on" => "search"]
        ];
    }

    public function reloadTranslations()
    {
        $translations = self::getDb()->table(BlogConfig::get()->tablesPrefix . 'categories_translations')->where("category_id = :id", [':id' => $this->id])->get();
        foreach ($translations as $trans) {
            $this->title[$trans['language']] = $trans['title'];
            $this->description[$trans['language']] = $trans['description'];
        }
    }

    public function beforeSave()
    {
        if ($this->isNewRecord()) {
            $this->added_by = WebApp::get()->user()->id;
            $this->added_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    public function saveTranslations()
    {
        self::getDb()->table(BlogConfig::get()->tablesPrefix . 'categories_translations')->where("category_id = :id", [':id' => $this->id])->delete();
        foreach ($this->title as $lang => $translation) {
            self::getDb()->table(BlogConfig::get()->tablesPrefix . 'categories_translations')->insert(['category_id' => $this->id, 'language' => $lang, 'title' => $translation, 'description' => $this->description[$lang]]);
        }
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "name", "added_by", "added_time"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    protected $translation;

    /**
     * @return string
     */
    public function getTitle()
    {
        if (!$this->translation) {
            $this->translation = self::getDb()->table(BlogConfig::get()->tablesPrefix . 'categories_translations')->where("category_id = :id AND language = :lang", [':id' => $this->id, ':lang' => BlogConfig::get()->getActiveLanguage()])->first();
        }

        return $this->translation['title'];
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if (!$this->translation) {
            $this->translation = self::getDb()->table(BlogConfig::get()->tablesPrefix . 'categories_translations')->where("category_id = :id AND language = :lang", [':id' => $this->id, ':lang' => BlogConfig::get()->getActiveLanguage()]);
        }
        return $this->translation['description'];
    }
}
