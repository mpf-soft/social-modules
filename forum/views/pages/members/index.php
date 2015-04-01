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
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " Members", $menu); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>
    <?php if ((!\mpf\modules\forum\components\UserAccess::get()->isMember($this->sectionId)
        || \mpf\modules\forum\components\UserAccess::get()->isBanned($this->sectionId) )
        && !\mpf\modules\forum\components\UserAccess::get()->isSiteModerator()
        && !\mpf\modules\forum\components\UserAccess::get()->isSiteAdmin()) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'members']); ?>
        <?php return; ?>
    <?php } ?>

    <?php
        \mpf\widgets\datatable\Table::get([
            'dataProvider' => $model->getDataProvider($this->sectionId),
            'columns' => [
                'user_id' => [
                    'value' => '$row->getProfileLink()'
                ],
                'member_since' =>[
                    'class' => 'Date'
                ],
                'title_id' => [
                    'value' => '$row->title_id?$row->title->title:"-"',
                    'filter' => \mpf\helpers\ArrayHelper::get()->transform(\mpf\modules\forum\models\ForumTitle::findAllByAttributes(['section_id' => $this->sectionId]), ['id' => 'title'])
                ],
                'group_id' => [
                    'value' => '$row->group->full_name',
                    'filter' => \mpf\helpers\ArrayHelper::get()->transform(\mpf\modules\forum\models\ForumUserGroup::findAllBySection($this->sectionId), ['id' => 'full_name'])
                ],
                'muted' => [
                    'class' => 'YesNo'
                ],
                'banned' => [
                    'class' => 'YesNo'
                ]
            ]
        ])->display();
    ?>
</div>