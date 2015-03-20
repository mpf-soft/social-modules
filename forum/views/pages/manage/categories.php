<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumCategory */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - Categories & Subcategories", [
    [
        'url' => $this->updateURLWithSection(['manage', 'groups']),
        'label' => 'Manage Groups'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'categories']),
        'label' => 'Manage Categories',
        'htmlOptions' => ['class' => 'selected']
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newCategory']),
        'label' => 'New Category'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newSubcategory']),
        'label' => 'New Subcategory'
    ]
]); ?>
<?php
\mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider(),
    'columns' => [
        'name',
        'url_friendly_name',
        'user_id' => [
            'value' => '$row->author->name'
        ],
        [
            'class' => 'Actions',
            'buttons' => [
                'delete' => ['class' => 'Delete', 'iconSize' => 22, 'url' => $this->getUrlForDatatableAction('delete')],
                'edit' => ['class' => 'Edit', 'iconSize' => 22, 'url' => $this->getUrlForDatatableAction('editCategory')],
                'subcategories' => [
                    'iconSize' => 22,
                    'icon' => '%MPF_ASSETS%images/oxygen/%SIZE%/actions/format-list-unordered.png',
                    'url' => $this->getUrlForDatatableAction('subcategories', [], null, 'category'),
                    'title' => '"Subcategories"'
                ]
            ]
        ]
    ]
])->display();
?>