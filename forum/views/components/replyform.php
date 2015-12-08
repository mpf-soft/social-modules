<?php /* @var $model \mpf\modules\forum\models\ForumReply */ ?>
<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>

<?= \mpf\widgets\form\Form::get([
    'name'=> isset($name)?$name:'reply',
    'model' => $model,
    'theme' => 'default-wide',
    'action' => $model->isNewRecord()?$this->updateURLWithSection(['thread', 'reply']):null,
    'hiddenInputs' => [
        'parent' => isset($parent)?$parent:0,
        'level' => isset($level)?$level:1,
        'thread_id' => $model->thread_id
    ],
    'fields' => [
        [
            'name' => 'content',
            'type' => \mpf\modules\forum\components\Config::value("FORUM_REPLY_INPUT_TYPE"),
            'htmlOptions' => ['style' => 'min-height: ' . (isset($height)?$height:'150px;')]
        ]
    ],
    'links' => isset($cancel)?[
        'Cancel' => [
            'href' => '#',
            'onclick' => 'return hideReplyForm(this.parentNode.parentNode);'
        ]
    ]:[]
])->display(); ?>