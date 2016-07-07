<?php if (\mpf\modules\blog\components\BlogConfig::get()->customFooter) { ?>
    <?php require_once \mpf\modules\blog\components\BlogConfig::get()->customFooter; ?>
    <?php return; ?>
<?php } ?>
    &nbsp;
</div>
<div id="side-blog-section">

    <?php if (\mpf\modules\blog\components\BlogConfig::get()->showSideSearch) { ?>
        <div class="blog-side-section">
            <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t('Search'); ?></h2>
            <div class="blog-side-section-content">
                <form method="get" class="blog-search-form"
                      action="<?= \mpf\WebApp::get()->request()->createURL('search', 'index'); ?>">
                    <input type="text" name="text" class="blog-search-form-text"
                           placeholder="<?= \app\components\htmltools\Translator::get()->t('Text To Search'); ?>">
                    <input type="submit" class="blog-button blog-search-form-submit-button"
                           value="<?= \app\components\htmltools\Translator::get()->t('Search'); ?>"/>
                </form>
            </div>
        </div>
    <?php } ?>
    <?php if (\mpf\modules\blog\components\UserAccess::canWrite()) { ?>
        <div class="blog-side-section">
            <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t('Control Panel'); ?></h2>
            <div class="blog-side-section-content">
                <?= \mpf\web\helpers\Html::get()->link(['articles', 'add'], \app\components\htmltools\Translator::get()->t('Write A New Article'), ['class' => 'blog-button blog-write-new-button']); ?>
                <?= \mpf\web\helpers\Html::get()->link(['articles', 'index'], \app\components\htmltools\Translator::get()->t('My Articles'), ['class' => 'blog-my-articles-link']); ?>
            </div>
        </div>
    <?php } ?>

    <?php if (\mpf\modules\blog\components\BlogConfig::get()->showSideCategories) { ?>
        <div class="blog-side-section">
            <h2 class="blog-side-section-title">
                <?= \app\components\htmltools\Translator::get()->t('Categories'); ?>
                <?php if (\mpf\modules\blog\components\UserAccess::canEditCategories()) { ?>
                    <?= \mpf\web\helpers\Html::get()->link(['categories', 'index'], \mpf\web\helpers\Html::get()->mpfImage('oxygen/22x22/actions/configure.png', \app\components\htmltools\Translator::get()->t('Manage Categories')), ['class' => 'blog-manage-categories-link', 'title' => \app\components\htmltools\Translator::get()->t('Manage Categories')]); ?>
                <?php } ?>
            </h2>
            <div class="blog-side-section-content">
                <ul class="blog-categories-list">
                    <?php foreach (\mpf\modules\blog\models\BlogCategory::findAll() as $category) { ?>
                        <li>
                            <?= \mpf\web\helpers\Html::get()->link(['home', 'category', ['id' => $category->id, 'name' => $category->name]], $category->getTitle()); ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    <?php } ?>

    <?php foreach (\mpf\modules\blog\components\BlogConfig::get()->customSidePanels as $title => $content) { ?>
        <div class="blog-side-section">
            <h2 class="blog-side-section-title"><?= \app\components\htmltools\Translator::get()->t($title); ?></h2>
            <div class="blog-side-section-content">
                <?= call_user_func($content); ?>
            </div>
        </div>
    <?php } ?>
</div>
</div>