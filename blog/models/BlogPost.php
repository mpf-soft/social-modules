<?php
/**
 * Created by MPF Framework.
 * Date: 2016-07-07
 * Time: 08:41
 */

namespace mpf\modules\blog\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\helpers\FileHelper;
use mpf\modules\blog\components\BlogConfig;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\fields\Markdown;

/**
 * Class BlogPost
 * @package app\models
 * @property int $id
 * @property int $author_id
 * @property int $category_id
 * @property string $time_written
 * @property string $time_published
 * @property int $status
 * @property int $edited_by
 * @property string $edit_time
 * @property int $edit_number
 * @property string $url
 * @property string $image_icon
 * @property string $image_cover
 * @property int $allow_comments
 * @property \app\models\User $author
 * @property \mpf\modules\blog\models\BlogCategory $category
 * @property \app\models\User $editor
 * @property \mpf\modules\blog\models\BlogComment[] $comments
 */
class BlogPost extends DbModel
{

    const STATUS_NEW = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DELETED = 2;

    public $keywords, $title = [], $content = [];

    /**
     * @return $this
     */
    public function beforeEdit()
    {
        $translations = self::getDb()->table('blog_posts_translations')->where("post_id = :id", [':id' => $this->id])->get();
        foreach ($translations as $trans) {
            $this->title[$trans['language']] = $trans['title'];
            $this->content[$trans['language']] = $trans['content'];
        }
        $words = self::getDb()->table('blog_posts_keywords')->where('post_id = :post', [':post' => $this->id])->get();
        $this->keywords = [];
        foreach ($words as $word) {
            $this->keywords[] = $word['keyword'];
        }
        $this->keywords = implode(', ', $this->keywords);
        return $this;
    }


    /**
     * @return string[]
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_DELETED => 'Deleted'
        ];
    }

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "blog_posts";
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
            'author_id' => 'Author',
            'category_id' => 'Category',
            'time_written' => 'Time Written',
            'time_published' => 'Time Published',
            'status' => 'Status',
            'edited_by' => 'Edited By',
            'edit_time' => 'Edit Time',
            'edit_number' => 'Edit Number',
            'url' => 'URL Friendly Title',
            'image_icon' => 'Image Icon',
            'image_cover' => 'Image Cover',
            'allow_comments' => 'Allow Comments'
        ];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return [
            'author' => [DbRelations::BELONGS_TO, '\app\models\User', 'author_id'],
            'category' => [DbRelations::BELONGS_TO, '\mpf\modules\blog\models\BlogCategory', 'category_id'],
            'comments' => [DbRelations::HAS_MANY, BlogComment::className(), 'post_id'],
            'editor' => [DbRelations::BELONGS_TO, '\app\models\User', 'edited_by']
        ];
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules()
    {
        return [
            ["id, author_id, category_id, time_written, time_published, status, edited_by, edit_time, edit_number, url, image_icon, image_cover, allow_comments", "safe", "on" => "search"]
        ];
    }


    /**
     * @return string[]
     */
    public static function getFormFields()
    {
        $f = [
            'url',
            [
                'name' => 'allow_comments',
                'type' => 'checkbox'
            ],
            [
                'name' => 'category_id',
                'type' => 'select',
                'options' => \mpf\helpers\ArrayHelper::get()->transform(\mpf\modules\blog\models\BlogCategory::findAll(), ['id' => 'name'])
            ],
            [
                'name' => 'image_icon',
                'type' => 'image',
                'urlPrefix' => BlogConfig::get()->articleImageURL
            ],
            [
                'name' => 'image_cover',
                'type' => 'image',
                'urlPrefix' => BlogConfig::get()->articleImageURL
            ],
            [
                'name' => 'keywords',
                'type' => 'seoKeywords'
            ]
        ];
        foreach (BlogConfig::get()->languages as $lang) {
            $f[] = 'title[' . $lang . ']';
            $f[] = [
                'name' => 'content[' . $lang . ']',
                'type' => 'markdown'
            ];
        }
        return $f;
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider()
    {
        $condition = new ModelCondition(['model' => __CLASS__]);

        foreach (["id", "author_id", "category_id", "time_written", "time_published", "status", "edited_by", "edit_time", "edit_number", "url", "allow_comments"] as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider([
            'modelCondition' => $condition
        ]);
    }

    public function afterSave()
    {
        $this->updateImages();
        self::getDb()->table('blog_posts_keywords')->where('post_id = :post', [':post' => $this->id])->delete();
        $keywords = explode(', ', $this->keywords);
        foreach ($keywords as $word)
            self::getDb()->table('blog_posts_keywords')->insert(['post_id' => $this->id, 'keyword' => $word]);
        self::getDb()->table('blog_posts_translations')->where('post_id = :post', [':post' => $this->id])->delete();
        foreach (BlogConfig::get()->languages as $language) {
            self::getDb()->table('blog_posts_translations')->insert([
                'post_id' => $this->id,
                'language' => $language,
                'title' => $this->title[$language],
                'content' => $this->content[$language]
            ]);
        }
        return true;
    }

    /**
     * @return $this
     */
    public function updateImages()
    {
        $updates = false;
        foreach (["image_icon", "image_cover"] as $key) {
            if (!isset($_FILES[$key]) || !$_FILES[$key]['tmp_name'])
                continue;
            if (!FileHelper::get()->isImage($_FILES[$key]['tmp_name']))
                continue;
            if (!file_exists($_FILES[$key]['tmp_name']))
                continue;
            $name = $this->id . '_' . $key . substr($_FILES[$key]['name'], -50);
            if (FileHelper::get()->upload($key, dirname(APP_ROOT) . '/htdocs/' . BlogConfig::get()->articleImageLocation . $name)) {
                $old = $this->$key;
                if ('default.png' != $old && $old != $name) {
                    @unlink(dirname(APP_ROOT) . '/htdocs/' . BlogConfig::get()->articleImageLocation . $old);
                }
                $this->$key = $name;
                $updates = true;
            }
        }
        if ($updates) {
            $this->save();
        }
        return $this;
    }

    public static $totalResults;

    public static function getForSearch($text, $page)
    {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn("status", self::STATUS_PUBLISHED);
        $condition->with = ['author', 'category'];
        $condition->limit = BlogConfig::get()->postsPerPage;
        $condition->order = '`t`.`id` DESC';
        $condition->offset = ($page - 1) * BlogConfig::get()->postsPerPage;
        $r = self::findAll($condition);
        self::$totalResults = self::count($condition);
        return $r;
    }

    public static function getLatestPublished($page)
    {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn("status", self::STATUS_PUBLISHED);
        $condition->with = ['author', 'category'];
        $condition->limit = BlogConfig::get()->postsPerPage;
        $condition->order = '`t`.`id` DESC';
        $condition->offset = ($page - 1) * BlogConfig::get()->postsPerPage;
        $r = self::findAll($condition);
        self::$totalResults = self::count($condition);
        return $r;
    }

    public static function getForCategory($category, $page)
    {
        $condition = new ModelCondition(['model' => __CLASS__]);
        $condition->compareColumn('category_id', $category);
        $condition->compareColumn("status", self::STATUS_PUBLISHED);
        $condition->with = ['author', 'category'];
        $condition->limit = BlogConfig::get()->postsPerPage;
        $condition->order = '`t`.`id` DESC';
        $condition->offset = ($page - 1) * BlogConfig::get()->postsPerPage;
        $r = self::findAll($condition);
        self::$totalResults = self::count($condition);
        return $r;
    }

    protected $translation;

    /**
     * @return string
     */
    public function getTitle()
    {
        if (!$this->translation) {
            $this->translation = self::getDb()->table('blog_posts_translations')->where("post_id = :id AND language = :lang", [':id' => $this->id, ':lang' => BlogConfig::get()->getActiveLanguage()])->first();
        }

        return $this->translation['title'];
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getContent($full = true)
    {
        if (!$this->translation) {
            $this->translation = self::getDb()->table('blog_posts_translations')->where("post_id = :id AND language = :lang", [':id' => $this->id, ':lang' => BlogConfig::get()->getActiveLanguage()])->first();
        }
        $content = $this->translation['content'];
        if (!$full) {
            $content = explode(BlogConfig::get()->introductionSeparator, $content, 2);
            $content = $content[0];
        } else {
            $content = str_replace(BlogConfig::get()->introductionSeparator, "", $content);
        }
        return Markdown::processText($content) .
        Html::get()->scriptFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/highlight.pack.js') .
        Html::get()->cssFile(WebApp::get()->request()->getWebRoot() . 'main/highlight/styles/github.css') .
        Html::get()->script('hljs.tabReplace = \'    \';hljs.initHighlightingOnLoad();');
    }

    /**
     * @param array $htmlOptions
     * @return string
     */
    public function getIcon($htmlOptions = [])
    {
        if (!$this->image_icon)
            return "";
        return Html::get()->image(BlogConfig::get()->articleImageURL . $this->image_icon, $this->getTitle(), $htmlOptions);
    }

    /**
     * @param array $htmlOptions
     * @return string
     */
    public function getCover($htmlOptions = [])
    {
        if (!$this->image_cover)
            return "";
        return Html::get()->image(BlogConfig::get()->articleImageURL . $this->image_cover, $this->getTitle(), $htmlOptions);
    }

    public function getAuthorIcon()
    {
        return WebApp::get()->request()->getWebRoot() . BlogConfig::get()->userAvatarURL . ($this->author->icon ?: 'default.png');
    }
}
