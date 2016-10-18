<?php require_once dirname(dirname(__DIR__)) . '/layout/header.php'; ?>

<div class="blog-article">
    <h1 class="blog-article-title"><?= \mpf\web\helpers\Html::get()->link(['home', 'read', ['id' => $article->id]], $article->getTitle()); ?></h1>
    <div class="blog-article-header">
        <?= \app\components\htmltools\Translator::get()->t("Written by"); ?>
        <?= $article->author->name; ?>
        <?= \mpf\helpers\DateTimeHelper::get()->niceDate($article->time_published); ?>
    </div>
    <div class="blog-article-content">
        <?= $article->getIcon(); ?>
        <?= $article->getContent(false); ?>
    </div>
</div>

<?php if (\mpf\modules\blog\components\BlogConfig::get()->allowComments) { ?>
    <div class="blog-article-comments">
        <?php if ($article->allow_comments) { ?>
            <div class="comment-form">
                <?php if (\mpf\WebApp::get()->user()->isGuest()) { ?>
                    <?= \mpf\widgets\form\Form::get([
                        'name' => 'save',
                        'model' => $model,
                        'theme' => 'default-wide',
                        'fields' => [
                            'username',
                            'email',
                            [
                                'name' => 'text',
                                'type' => 'textarea'
                            ]
                        ]
                    ])->display(); ?>
                <?php } else { ?>
                    <?= \mpf\widgets\form\Form::get([
                        'name' => 'save',
                        'model' => $model,
                        'theme' => 'default-wide',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'textarea'
                            ]
                        ]
                    ])->display(); ?>
                <?php } ?>
            </div>

            <div class="blog-comments-list">
                <?php foreach ($comments as $comment) { ?>
                    <div class="comment">
                        <b><?= $comment->username; ?></b>
                        <?= htmlentities($comment->text); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <i><?= \app\components\htmltools\Translator::get()->t("Comments are hidden for this article!"); ?></i>
        <?php } ?>
    </div>
<?php } ?>
<?php require_once dirname(dirname(__DIR__)) . '/layout/footer.php'; ?>
