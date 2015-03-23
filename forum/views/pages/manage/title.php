<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumTitle */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - Users", [
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
]); ?>
<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => [
        'title'
    ]
])->display(); ?>