<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumSubcategory */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \app\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " New subcategory", [
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
        'url' => $this->updateURLWithSection(['manage', 'newSubcategory']),
        'label' => 'New Subcategory',
        'htmlOptions' => ['class' => 'selected']
    ]
]); ?>
<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'formHtmlOptions' => ['enctype' => 'multipart/form-data'],
    'fields' => [
        'title',
        'url_friendly_title',
        'description',
        [
            'name' => 'category_id',
            'type' => 'select',
            'options' => \mpf\helpers\ArrayHelper::get()->transform(\app\modules\forum\models\ForumCategory::findAllBySection($this->sectionId), ['id' => 'name']),
            [
                'name' => 'icon',
                'type' => 'image',
                'urlPrefix' => $this->getUploadUrl() . 'subcategories/'
            ]
        ]
    ]
])->display(); ?>