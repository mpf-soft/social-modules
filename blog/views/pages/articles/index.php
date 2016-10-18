<?php require_once dirname(dirname(__DIR__)) . '/layout/header.php'; ?>
<?= \mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'url',
        'author_id' => [
            'filter' => \mpf\helpers\ArrayHelper::get()->transform(\app\models\User::findAll(), ['id' => 'name']),
            'value' => function (\mpf\modules\blog\models\BlogPost $model) {
                return $model->author->name;
            }
        ],
        'category_id' => [
            'filter' => \mpf\helpers\ArrayHelper::get()->transform(\mpf\modules\blog\models\BlogCategory::findAll(), ['id' => 'name']),
            'value' => function (\mpf\modules\blog\models\BlogPost $model) {
                return $model->category->name;
            }
        ],
        'time_written' => ['class' => 'Date'],
        'time_published' => ['class' => 'Date'],
        'status' => [
            'filter' => \mpf\modules\blog\models\BlogPost::getStatuses(),
            'value' => function (\mpf\modules\blog\models\BlogPost $model) {
                $all = \mpf\modules\blog\models\BlogPost::getStatuses();
                return $all[$model->status];
            }
        ],
        ['class' => 'Actions',
            'buttons' => [
                'delete' => ['class' => 'Delete'],
                'edit' => ['class' => 'Edit'],
                'publish' => [
                    'post' => ['{{modelKey}}' => '$row->id'],
                    'confirmation' => \app\components\htmltools\Translator::get()->t("Are you sure that you want to publish the article?"),
                    'title' => '"Publish Article"',
                    'url' => "\\mpf\\WebApp::get()->request()->createURL('articles', 'publish')",
                    'icon' => '%MPF_ASSETS%images/oxygen/%SIZE%/actions/view-task.png',
                    'visible' => "\$row->status != " . \mpf\modules\blog\models\BlogPost::STATUS_PUBLISHED
                ]
            ],
            'headerHtmlOptions' => [
                'style' => 'width:60px;'
            ]
        ]
    ]
])->display(); ?>
<?php require_once dirname(dirname(__DIR__)) . '/layout/footer.php'; ?>
