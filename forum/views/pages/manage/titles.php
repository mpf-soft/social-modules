<?php /* @var $this \mpf\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumTitle */ ?>
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
        'label' => 'Manage Users'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'titles']),
        'label' => 'Manage Titles',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newTitle']),
        'label' => 'New Title'
    ]
]); ?>
<?php
    \mpf\widgets\datatable\Table::get([
        'dataProvider' => $model->getDataProvider($this->sectionId),
        'columns' => [
            'id',
            'title',
            [
                'class' => 'Actions',
                'buttons' => [
                    'delete' => ['class' => 'Delete', 'url' => $this->getUrlForDatatableAction('delete')],
                    'edit' => ['class' => 'Edit', 'url' => $this->getUrlForDatatableAction('editTitle')]
                ]
            ]
        ]
    ])->display();
?>