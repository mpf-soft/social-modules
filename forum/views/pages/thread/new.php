<?php /* @var $this \app\modules\forum\controllers\Home */ ?>
<?php /* @var $subcategory \app\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $model \app\modules\forum\models\ForumThread */ ?>
<?php $menu = []; ?>
<?php if (\app\modules\forum\components\UserAccess::get()->isSectionAdmin($this->sectionId)) { ?>
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
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $subcategory->category->url_friendly_name, 'id' => $subcategory->category_id]]), $subcategory->category->name)
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $subcategory->category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->category_id]]), $subcategory->title)
    . " " . $this->pageTitleSeparator . " " . \app\modules\forum\components\Translator::get()->translate("New Thread"), $menu); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\app\modules\forum\components\UserAccess::get()->canCreateNewThread($subcategory->category_id)) { ?>
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
                'visible' => \app\modules\forum\components\UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)
            ]
        ]
    ])->display(); ?>

</div>