<?php /* @var $this \app\modules\forum\components\Controller */ ?>
<?php /* @var $model \app\modules\forum\models\ForumReply */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $model->thread->subcategory->category->url_friendly_name, 'id' => $model->thread->subcategory->category_id]]), $model->thread->subcategory->category->name)
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $model->thread->subcategory->category->url_friendly_name, 'subcategory' => $model->thread->subcategory->url_friendly_title, 'id' => $model->thread->subcategory->category_id]]), $model->thread->subcategory->title)
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['category' => $model->thread->subcategory->category->url_friendly_name, 'subcategory' => $model->thread->subcategory->url_friendly_title, 'id' => $model->thread_id]]), $model->thread->title)
    . " " . $this->pageTitleSeparator . " "
    . \app\modules\forum\components\Translator::get()->translate("Edit reply")); ?>
<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php $this->displayComponent("replyform", ['model' => $model, 'height' => '300px;']); ?>
</div>