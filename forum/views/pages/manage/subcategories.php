<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $category \app\modules\forum\models\ForumCategory */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " - " . $category->name . " - Subcategories", [
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
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newCategory']),
        'label' => 'New Category'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newSubcategory', ['category' => $category->id]]),
        'label' => 'New Subcategory'
    ]
]); ?>
<?php
\mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider($category->id),
    'columns' => [
        'title',
        'user_id' => [
            'value' => '$row->owner->name'
        ],
        'description',
        [
            'class' => 'Actions',
            'buttons' => [
                'delete' => ['class' => 'Delete', 'iconSize' => 22, 'url' => $this->getUrlForDatatableAction('delete')],
                'edit' => ['class' => 'Edit', 'iconSize' => 22, 'url' => $this->getUrlForDatatableAction('editSubcategory')]
            ]
        ]
    ]
])->display();
?>