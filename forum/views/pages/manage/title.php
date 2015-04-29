<?php /* @var $this \mpf\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumTitle */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " Users",
    \mpf\modules\forum\components\UserAccess::get()->isSectionAdmin($this->sectionId) ?
        [
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
                'url' => $this->updateURLWithSection(['manage', 'newTitle']),
                'label' => 'New Title',
                'htmlOptions' => ['class' => 'selected']
            ]
        ] : [
        [
            'url' => $this->updateURLWithSection(['manage', 'users']),
            'label' => 'Manage Users'
        ],
        [
            'url' => $this->updateURLWithSection(['manage', 'titles']),
            'label' => 'Manage Titles',
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
        [
            'name' => 'icon',
            'type' => 'image',
            'urlPrefix' => $this->getUploadUrl() . 'titles/'
        ]
    ]
])->display(); ?>