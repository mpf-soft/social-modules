<?php /* @var $this \mpf\modules\forum\components\Controller */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumThread */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), $this->forumTitle)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'id' => $model->subcategory->category_id]]), $model->subcategory->category->name)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'subcategory' => $model->subcategory->url_friendly_title, 'id' => $model->subcategory->category_id]]), $model->subcategory->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'subcategory' => $model->subcategory->url_friendly_title, 'id' => $model->id]]), $model->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\modules\forum\components\Translator::get()->translate("Edit post")); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?= \mpf\widgets\form\Form::get([
        'model' => $model,
        'name' => 'save',
        'theme' => 'default-wide',
        'fields' => [
            'title',
            [
                'name' => 'content',
                'type' => \mpf\modules\forum\components\Config::value("FORUM_REPLY_INPUT_TYPE"),
                'htmlOptions' => ['style' => 'min-height: 250px;']
            ],
            [
                'name' => 'keywords',
                'type' => 'seoKeywords'
            ]
        ],
        'buttons' => [
            [
                'name' => 'preview',
                'label' => 'Preview'
            ],
            [
                'name' =>'sticky',
                'label' => 'Save as Sticky',
                'visible' => \mpf\modules\forum\components\UserAccess::get()->isCategoryModerator($model->subcategory->category_id, $this->sectionId)
            ]
        ]
    ])->display(); ?>

</div>