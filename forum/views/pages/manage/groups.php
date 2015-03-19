<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumUserGroup */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - User Groups", [
    [
        'url' => ['manage', 'groups'],
        'label' => 'Manage Groups'
    ],
    [
        'url' => ['manage', 'categories'],
        'label' => 'Manage Categories',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => ['manage', 'newGroup'],
        'label' => 'New Group'
    ]
]); ?>
<?php
\mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'full_name',
        'html_class',
        'admin' => ['class' => 'YesNo'],
        'moderator' => ['class' => 'YesNo'],
        'newthread' => ['class' => 'YesNo'],
        'threadreply' => ['class' => 'YesNo'],
        'canread' => ['class' => 'YesNo'],
        [
            'class' => 'Actions',
            'buttons' => [
                'delete' => ['class' => 'Delete', 'iconSize' => 32],
                'view' => ['class' => 'View', 'iconSize' => 32]
            ]
        ]
    ]
])->display();
?>