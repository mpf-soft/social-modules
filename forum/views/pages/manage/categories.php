<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $categories \app\modules\forum\models\ForumCategory[] */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - Categories & Subcategories", [
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
        'url' => '#',
        'label' => 'New Category',
        'htmlOptions' => [
            'onclick' => 'return createCategory();'
        ]
    ],
    [
        'url' => ['manage', 'newSubcategory'],
        'label' => 'New Subcategory'
    ]
]); ?>
<?= \mpf\web\helpers\Form::get()->openForm(['method' => 'post', 'style' => 'display:none;', 'id' => 'create-category']); ?>
    <input type="hidden" id="category-name" value="" name="name"/>
<?= \mpf\web\helpers\Form::get()->closeForm(); ?>
    <script>
        function createCategory() {
            var name = prompt("Please enter category name:");
            if (!name || name.length < 2) {
                alert("Invalid name! Minimum 2 characters required!");
                return false;
            }
            $('#category-name').val(name);
            $('#create-category').submit();
            return false;
        }
    </script>
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