<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\models\User */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - Users", [
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
        'url' => $this->updateURLWithSection(['manage', 'newGroup']),
        'label' => 'New Group'
    ]
]); ?>

<?php
\mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'name',
        'register_date',
        'last_login',
        'title_id' => [
            'class' => 'InlineEdit',
            'type' => 'select',
            'options' => \mpf\helpers\ArrayHelper::get()->transform(\app\models\UserTitle::findAll(), ['id' =>'title'])
        ],
        [
            'value' => ''
        ]
    ]
])->display();
?>