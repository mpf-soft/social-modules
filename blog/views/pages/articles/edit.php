<?php require_once dirname(dirname(__DIR__)) . '/layout/header.php'; ?>
<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'formHtmlOptions' => ['enctype' => 'multipart/form-data'],
    'fields' => \mpf\modules\blog\models\BlogPost::getFormFields()
])->display(); ?>
<?php require_once dirname(dirname(__DIR__)) . '/layout/footer.php'; ?>
