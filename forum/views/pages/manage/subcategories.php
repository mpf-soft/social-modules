<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $category \app\modules\forum\models\ForumCategory */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - " . $category->name . " - Subcategories", [
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
        'label' => 'New Category',
        'htmlOptions' => [
            'onclick' => 'return createCategory();'
        ]
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
        'title',
        'category_id' => [
            'value' => '$row->category->name',
            'filter' => \mpf\helpers\ArrayHelper::get()->transform(\app\modules\forum\models\ForumCategory::findAll(), ['id' => 'name'])
        ],
        'user_id' => [
            'value' => '$row->owner->name'
        ],
        'description',
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