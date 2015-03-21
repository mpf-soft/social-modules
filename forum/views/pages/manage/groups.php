<?php /* @var $this \app\modules\forum\controllers\Manage */ ?>
<?php /* @var $model \app\modules\forum\models\ForumUserGroup */ ?>
<?= \app\components\htmltools\Page::get()->title("Forum - User Groups", [
    [
        'url' => $this->updateURLWithSection(['manage', 'groups']),
        'label' => 'Manage Groups',
        'htmlOptions' => ['class' => 'selected']
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
        'url' => $this->updateURLWithSection(['manage', 'newGroup']),
        'label' => 'New Group'
    ]
]); ?>
    <div class="forum-default-group-line">
        <span>Default group:</span>
        <a href="#"><?= \app\modules\forum\models\ForumSection::findByPk($this->sectionId)->defaultGroup->full_name; ?></a>
        <?= \mpf\web\helpers\Form::get()->openForm(['method' => 'post', 'style' => 'display:none;']); ?>
        <?= \mpf\web\helpers\Form::get()->select('group', \mpf\helpers\ArrayHelper::get()->transform(\app\modules\forum\models\ForumUserGroup::findAllByAttributes(['section_id' => $this->sectionId]), ['id' => 'full_name'])); ?>
        <input type="submit" name="changedefaultgroup" value="Update"/>
        <?= \mpf\web\helpers\Form::get()->closeForm(); ?>
    </div>

<?php
\mpf\widgets\datatable\Table::get([
    'dataProvider' => $model->getDataProvider($this->sectionId),
    'columns' => [
        'full_name',
        'html_class',
        'admin' => ['class' => 'YesNo'],
        'moderator' => ['class' => 'YesNo'],
        'newthread' => ['class' => 'YesNo'],
        'threadreply' => ['class' => 'YesNo'],
        'canread' => ['class' => 'YesNo'],
        [
            'class' => 'Actions',
            'buttons' => [
                'delete' => ['class' => 'Delete', 'iconSize' => 22, 'url' => $this->getUrlForDatatableAction('delete')],
                'edit' => [
                    'class' => 'Edit',
                    'iconSize' => 22,
                    'url' => $this->getUrlForDatatableAction('editGroup')
                ]
            ]
        ]
    ]
])->display();
?>
<script>
    $(document).ready(function(){
        $('.forum-default-group-line a').click(function(){
            $(this).hide();
            $('form', this.parentNode).show();
            return false;
        })
    })
</script>