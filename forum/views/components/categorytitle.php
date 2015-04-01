<?php /* @var $category \mpf\modules\forum\models\ForumCategory */ ?>
<?php /* @var $this \mpf\modules\forum\components\Controller */ ?>
<h2><?= \mpf\web\helpers\Html::get()->link(
        $this->updateURLWithSection(['category', 'index', ['category' => $category->url_friendly_name, 'id' => $category->id]]),
        \mpf\web\helpers\Html::get()->image($this->getUploadUrl() . 'categories/' . $category->icon) . $category->name,
        ['class' => 'category']
    ); ?></h2>