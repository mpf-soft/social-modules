<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumTitle */ ?>
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