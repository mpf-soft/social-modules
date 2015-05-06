<?php /* @var $this \mpf\modules\forum\components\Controller */ ?>
<?php /* @var $model \mpf\modules\forum\models\ForumThread */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'id' => $model->subcategory->category_id]]), $model->subcategory->category->name)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'subcategory' => $model->subcategory->url_friendly_title, 'id' => $model->subcategory->category_id]]), $model->subcategory->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['category' => $model->subcategory->category->url_friendly_name, 'subcategory' => $model->subcategory->url_friendly_title, 'id' => $model->id]]), $model->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\modules\forum\components\Translator::get()->translate("Move post")); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('searchbar'); ?>
    <?php $this->displayComponent('topuserpanel'); ?>
    <?= \mpf\widgets\form\Form::get([
        'name' => 'move',
        'model' => $model,
        'theme' => 'default-wide',
        'fields' => [
            [
                'name' => 'subcategory_id',
                'type' => 'select',
                'options' => \mpf\modules\forum\models\ForumSubcategory::getAllForSelectTree($this->sectionId)
            ]
        ]
    ])->display(); ?>
</div>