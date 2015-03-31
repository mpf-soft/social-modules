<?php /* @var $this \app\modules\forum\controllers\Home */ ?>
<?php /* @var $model \app\modules\forum\models\ForumUser2Section */ ?>
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
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \app\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " Control Panel", $menu); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>
    <?php if ((!\app\modules\forum\components\UserAccess::get()->isMember($this->sectionId)
            || \app\modules\forum\components\UserAccess::get()->isBanned($this->sectionId) )
        && !\app\modules\forum\components\UserAccess::get()->isSiteModerator()
        && !\app\modules\forum\components\UserAccess::get()->isSiteAdmin()) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'members']); ?>
        <?php return; ?>
    <?php } ?>

    <?= \mpf\widgets\form\Form::get([
        'name' => 'save',
        'model' => $model,
        'theme' => 'default-wide',
        'formHtmlOptions' => ['enctype' => 'multipart/form-data'],
        'fields' => [
            [
                'name' => 'signature',
                'type' => 'forumTextarea',
                'tags' => \app\models\PageTag::getTagHints(true),
                'htmlOptions' => ['style' => 'min-height: 200px;']
            ],
            [
                'name' => 'icon',
                'type' => 'image',
                'urlPrefix' => $model->getIconLocationURL()
            ]
        ]
    ])->display(); ?>

</div>