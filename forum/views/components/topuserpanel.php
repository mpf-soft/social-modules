<?php if (\mpf\WebApp::get()->user()->isGuest()) { ?>
    <?php return; ?>
<?php } ?>
<div class="forum-user-panel">
    <div class="forum-user-panel-icon">
        <?= \mpf\web\helpers\Html::get()->link(['user', 'controlPanel'], \mpf\web\helpers\Html::get()->image(\mpf\modules\forum\components\ModelHelper::getUserIconURL(\mpf\WebApp::get()->user()->icon ?: 'default.png'))); ?>
    </div>
    <div class="forum-user-panel-about">
        <?= \mpf\web\helpers\Html::get()->link(['user', 'index', ['id' => \mpf\WebApp::get()->user()->id, 'name' => \mpf\WebApp::get()->user()->name]], \mpf\WebApp::get()->user()->name, ['class' => 'form-user-panel-about-name']); ?>
        <span
            class="form-user-panel-about-title"><?= \mpf\modules\forum\components\UserAccess::get()->getUserTitle($this->sectionId, true); ?></span>
    </div>
    <div class="forum-user-panel-links">
        <?= \mpf\web\helpers\Html::get()->link(['home'], \mpf\modules\forum\components\Translator::get()->translate('Categories')); ?>
        <?= \mpf\web\helpers\Html::get()->link(['user', 'controlPanel'], \mpf\modules\forum\components\Translator::get()->translate('Control Panel')); ?>
        <?= \mpf\web\helpers\Html::get()->link(['search', 'recent'], \mpf\modules\forum\components\Translator::get()->translate('Recent Threads')); ?>
        <?= \mpf\web\helpers\Html::get()->link(['members'], \mpf\modules\forum\components\Translator::get()->translate('Members')); ?>
    </div>
</div>