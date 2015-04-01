<?php /* @var $this \mpf\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumUserGroup */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " User Groups " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " " . ($model->isNewRecord()?'New Group':'Edit ' . $model->full_name), [
    [
        'url' => ['manage', 'groups'],
        'label' => 'Manage Groups'
    ],
    [
        'url' => ['manage', 'categories'],
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
        'url' => ['manage', 'newGroup'],
        'label' => 'New Group',
        'htmlOptions' => $model->isNewRecord()?['class' => 'selected']:[]
    ]
]); ?>
<?= \mpf\widgets\form\Form::get([
    'model' => $model,
    'name' => 'save',
    'theme' => 'default-wide',
    'fields' => [
        'full_name',
        'html_class',
        [
            'name' => 'admin',
            'type' => 'radio',
            'options' => ['No', 'Yes']
        ],
        [
            'name' => 'moderator',
            'type' => 'radio',
            'options' => ['No', 'Yes']
        ],
        [
            'name' => 'newthread',
            'type' => 'radio',
            'options' => ['No', 'Yes']
        ],
        [
            'name' => 'threadreply',
            'type' => 'radio',
            'options' => ['No', 'Yes']
        ],
        [
            'name' => 'canread',
            'type' => 'radio',
            'options' => ['No', 'Yes']
        ],

    ]
])->display(); ?>