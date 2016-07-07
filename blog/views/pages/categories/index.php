<?php require_once dirname(dirname(__DIR__)) . '/layout/header.php'; ?>
<?= \mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'name',
        'added_by' => [
            'filter' => \mpf\helpers\ArrayHelper::get()->transform(\app\models\User::findAll(), ['id' => 'name']),
            'value' => function (\mpf\modules\blog\models\BlogCategory $model) {
                return $model->user->name;
            }
        ],
        'added_time' => ['class' => 'Date'],
        [
            'class' => 'Actions',
            'buttons' => [
                'delete' => ['class' => 'Delete'],
                'edit' => ['class' => 'Edit']
            ],
            'headerHtmlOptions' => [
                'style' => 'width:60px;'
            ],
            'topButtons' => [
                'add' => ['class' => 'Add']
            ]
        ]
    ]
])->display(); ?>
<?php require_once dirname(dirname(__DIR__)) . '/layout/footer.php'; ?>


