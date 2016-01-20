<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumUser2Section */ ?>
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
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), $this->forumTitle) . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " Control Panel", $menu); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>
    <?php if ((!\mpf\modules\forum\components\UserAccess::get()->isMember($this->sectionId)
            || \mpf\modules\forum\components\UserAccess::get()->isBanned($this->sectionId) )
        && !\mpf\modules\forum\components\UserAccess::get()->isSiteModerator()
        && !\mpf\modules\forum\components\UserAccess::get()->isSiteAdmin()) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'members']); ?>
        <?php return; ?>
    <?php } ?>

    <?= \mpf\widgets\form\Form::get([
        'name' => 'save',
        'model' => $model,
        'theme' => 'default-wide',
        'formHtmlOptions' => ['enctype' => 'multipart/form-data'],
        'fields' => \mpf\modules\forum\components\Config::value('FORUM_HANDLE_USER_ICON')?[
            [
                'name' => 'signature',
                'type' => 'markdown',
                'htmlOptions' => ['style' => 'min-height: 200px;']
            ],
            [
                'name' => 'icon',
                'type' => 'image',
                'urlPrefix' => $model->getIconLocationURL()
            ]
        ]:[
            [
                'name' => 'signature',
                'type' => 'markdown',
                'htmlOptions' => ['style' => 'min-height: 200px;']
            ]
        ]
    ])->display(); ?>

</div>