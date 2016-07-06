</div>
<div id="side-blog-section">

    <div class="blog-side-section">
        <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t('Search'); ?></h2>
        <div class="blog-side-section-content">
            <form method="get" class="blog-search-form" action="<?= \mpf\WebApp::get()->request()->createURL('search', 'index'); ?>">
                <input type="text" name="text" class="blog-search-form-text"
                       placeholder="<?= \app\components\htmltools\Translator::get()->t('Text To Search'); ?>">
                <input type="submit" class="blog-button blog-search-form-submit-button" value="<?= \app\components\htmltools\Translator::get()->t('Search'); ?>"/>
            </form>
        </div>
    </div>

    <?php if (\mpf\modules\blog\components\UserAccess::canWrite()) { ?>
        <div class="blog-side-section">
            <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t('Control Panel'); ?></h2>
            <div class="blog-side-section-content">
                <?= \mpf\web\helpers\Html::get()->link(['articles', 'add'], \app\components\htmltools\Translator::get()->t('Write A New Article'), ['class' => 'blog-button blog-write-new-button']); ?>
                <?= \mpf\web\helpers\Html::get()->link(['articles', 'index'], \app\components\htmltools\Translator::get()->t('My Articles'), ['class' => 'blog-my-articles-link']); ?>
            </div>
        </div>
    <?php } ?>

    <div class="blog-side-section">
        <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t('Categories'); ?></h2>
        <div class="blog-side-section-content">

        </div>
    </div>

    <div class="blog-side-section">
        <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t('Links'); ?></h2>
        <div class="blog-side-section-content">

        </div>
    </div>
</div>
</div>