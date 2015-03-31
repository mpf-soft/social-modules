<?php /* @var $this \app\modules\forum\components\Controller */ ?>
<?php /* @var $model \app\modules\forum\models\ForumThread */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'id' => $model->subcategory->category_id]]), $model->subcategory->category->name)
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'subcategory' => $model->subcategory->url_friendly_title, 'id' => $model->subcategory->category_id]]), $model->subcategory->title)
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'subcategory' => $model->subcategory->url_friendly_title, 'id' => $model->id]]), $model->title)
    . " " . $this->pageTitleSeparator . " "
    . \app\modules\forum\components\Translator::get()->translate("Edit post")); ?>

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
                'type' => 'forumTextarea',
                'tags' => \app\models\PageTag::getTagHints(true),
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
                'visible' => \app\modules\forum\components\UserAccess::get()->isCategoryModerator($model->subcategory->category_id, $this->sectionId)
            ]
        ]
    ])->display(); ?>

</div>