<?php require_once dirname(dirname(__DIR__)) . '/layout/header.php'; ?>
<?php foreach ($articles as $article) {
    /* @var $article \mpf\modules\blog\models\BlogPost */ ?>
    <div class="blog-article">
        <h1 class="blog-article-title"><?= \mpf\web\helpers\Html::get()->link(['home', 'read', ['id' => $article->id]], $article->getTitle()); ?></h1>
        <div class="blog-article-header">
            <?= \app\components\htmltools\Translator::get()->t("Written by"); ?>
            <?= $article->anonimous?\app\components\htmltools\Translator::get()->t("Anonimous"):$article->author->name; ?>
            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($article->time_published); ?>
        </div>
        <div class="blog-article-content">
            <?= $article->getIcon(); ?>
            <?= $article->getContent(false); ?>
        </div>
        <div class="blog-article-footer">
            <?= \mpf\web\helpers\Html::get()->link(['home', 'read', ['id' => $article->id]], \app\components\htmltools\Translator::get()->t('Read entire article')); ?>
        </div>
    </div>
<?php } ?>
<?php require_once dirname(dirname(__DIR__)) . '/layout/footer.php'; ?>
