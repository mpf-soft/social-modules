<?php /* @var $this \mpf\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $category \mpf\modules\forum\models\ForumCategory */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " " . $category->name . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " Subcategories", [
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