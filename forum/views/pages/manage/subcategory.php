<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumSubcategory */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - " . $category->name . " - Subcategories", [
    [
        'url' => $this->updateURLWithSection(['manage', 'groups']),
        'label' => 'Manage Groups'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'categories']),
        'label' => 'Manage Categories'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newCategory']),
        'label' => 'New Category'
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newSubcategory', ['category' => $category->id]]),
        'label' => 'New Subcategory',
        'htmlOptions' => ['class' => 'selected']
    ]
]); ?>
<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model
])->display(); ?>