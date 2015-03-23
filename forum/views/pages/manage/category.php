<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumCategory */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - Categories - " . ($model->isNewRecord()?"Add New":"Edit " . $model->name), [
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
        'label' => 'New Category',
        'htmlOptions' => $model->isNewRecord()?['class' => 'selected']:[]
    ],
    [
        'url' => $this->updateURLWithSection(['manage', 'newSubcategory']),
        'label' => 'New Subcategory'
    ]
]); ?>

<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'formHtmlOptions' => ['enctype' => 'multipart/form-data'],
    'fields' => array_merge([
            'name',
            'url_friendly_name',
            'order',
            [
                'name' => 'icon',
                'type' => 'image',
                'urlPrefix' => $this->getUploadUrl() . 'categories/'
            ]
        ], $model->getGroupFields())
])->display(); ?>
