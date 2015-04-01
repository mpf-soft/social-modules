<?php if (\mpf\WebApp::get()->user()->isGuest()) { ?>
    <?php return ; ?>
<?php } ?>
<div class="forum-user-panel">
    <div class="forum-user-panel-icon">
        <?= \mpf\web\helpers\Html::get()->link(['user', 'controlPanel'], \mpf\web\helpers\Html::get()->image(\mpf\modules\forum\components\Config::value('USER_ICON_FOLDER_URL') . (\mpf\WebApp::get()->user()->icon?:'default.png'))); ?>
    </div>
    <div class="forum-user-panel-about">
        <?= \mpf\web\helpers\Html::get()->link(['user', 'profile', ['name' => \mpf\WebApp::get()->user()->name]], \mpf\WebApp::get()->user()->name, ['class' => 'form-user-panel-about-name']); ?>
        <span class="form-user-panel-about-title"><?= \mpf\modules\forum\components\UserAccess::get()->getUserTitle($this->sectionId, true); ?></span>
    </div>
    <div class="forum-user-panel-links">
        <?= \mpf\web\helpers\Html::get()->link(['home'], 'Categories'); ?>
        <?= \mpf\web\helpers\Html::get()->link(['user', 'controlPanel'], 'Control Panel'); ?>
        <?= \mpf\web\helpers\Html::get()->link(['search', 'recent'], 'Recent Threads'); ?>
        <?= \mpf\web\helpers\Html::get()->link(['members'], 'Members'); ?>
    </div>
</div>