<?php /* @var $this \mpf\modules\forum\components\Controller */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumReply */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $model->thread->subcategory->category->url_friendly_name, 'id' => $model->thread->subcategory->category_id]]), $model->thread->subcategory->category->name)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $model->thread->subcategory->category->url_friendly_name, 'subcategory' => $model->thread->subcategory->url_friendly_title, 'id' => $model->thread->subcategory->category_id]]), $model->thread->subcategory->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['category' => $model->thread->subcategory->category->url_friendly_name, 'subcategory' => $model->thread->subcategory->url_friendly_title, 'id' => $model->thread_id]]), $model->thread->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\modules\forum\components\Translator::get()->translate("Edit reply")); ?>
<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php $this->displayComponent("replyform", ['model' => $model, 'height' => '300px;']); ?>
</div>