<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumCategory */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - " . $model->name, [
    [
        'url' => ['manage', 'groups'],
        'label' => 'Manage Groups'
    ],
    [
        'url' => ['manage', 'categories'],
        'label' => 'Manage Categories'
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

<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array_merge([
            'name',
            'url_friendly_name',
            'order'
        ], $model->getGroupFields())
])->display(); ?>
