<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $subcategory \mpf\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumThread */ ?>
<?php $menu = []; ?>
<?php if (\mpf\modules\forum\components\UserAccess::get()->isSectionAdmin($this->sectionId)) { ?>
    <?php
    $menu = [
        [
            'url' => $this->updateURLWithSection(['manage', 'groups']),
            'label' => 'Manage Groups'
        ],
        [
            'url' => $this->updateURLWithSection(['manage', 'categories']),
            'label' => 'Manage Categories'
        ],
        [
            'url' => $this->updateURLWithSection(['manage', 'users']),
            'label' => 'Manage Users'
        ],
        [
            'url' => $this->updateURLWithSection(['manage', 'titles']),
            'label' => 'Manage Titles'
        ]

    ];
    ?>
<?php } ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), $this->forumTitle)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $subcategory->category->url_friendly_name, 'id' => $subcategory->category_id]]), $subcategory->category->name)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $subcategory->category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->category_id]]), $subcategory->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " " . \mpf\modules\forum\components\Translator::get()->translate("New Thread"), $menu); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\mpf\modules\forum\components\UserAccess::get()->canCreateNewThread($subcategory->category_id, $this->sectionId)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'newThread']); ?>
        <?php return; ?>
    <?php } ?>

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
                'visible' => \mpf\modules\forum\components\UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)
            ]
        ]
    ])->display(); ?>

</div>