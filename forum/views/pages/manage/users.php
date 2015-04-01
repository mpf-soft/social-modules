<?php /* @var $this \mpf\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumUser2Section */ ?>
<?php /* @var string[] $groups */ ?>
<?php /* @var string[] $titles */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " Users", [
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
        'label' => 'Manage Users',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'titles']),
        'label' => 'Manage Titles'
    ]
]); ?>

<?php
\mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider($this->sectionId, 'forum'),
    'columns' => [
        'name' => ['value' => '$row->user->name'],
        'member_since' => ['class' => 'Date'],
        'last_login' => ['class' => 'Date', 'value' => '$row->user->last_login?$row->user->last_login:"never"'],
        'title_id' => [
            'class' => 'InlineEdit',
            'type' => 'select',
            'options' => $titles,
            'filter' =>$titles
        ],
        'group_id' => [
            'class' => 'InlineEdit',
            'type' => 'select',
            'filter' => $groups,
            'options' => $groups
        ],
        'muted' => [
            'class' => 'InlineEdit',
            'type' => 'select',
            'filter' => ['No', 'Yes'],
            'options' => ['No', 'Yes']
        ],
        'banned' => [
            'class' => 'InlineEdit',
            'type' => 'select',
            'filter' => ['No', 'Yes'],
            'options' => ['No', 'Yes']
        ]
    ]
])->display();
?>