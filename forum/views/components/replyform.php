<?php /* @var $model \app\modules\forum\models\ForumReply */ ?>

<?= \mpf\widgets\form\Form::get([
    'name'=> 'reply',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => [
        [
            'name' => 'content',
            'type' => 'forumTextarea',
            'tags' => \app\models\PageTag::getTagHints(true),
            'htmlOptions' => ['style' => 'min-height: 150px;']
        ]
    ]
])->display(); ?>