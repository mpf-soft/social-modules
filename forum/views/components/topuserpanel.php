<?php if (\mpf\WebApp::get()->user()->isGuest()) { ?>
    <?php return ; ?>
<?php } ?>
<div class="forum-user-panel">
    <div class="forum-user-panel-icon">
        <?= \mpf\web\helpers\Html::get()->link(['user', 'controlPanel'], \mpf\web\helpers\Html::get()->image("https://robertsspaceindustries.com/media/7psqknsdcm40ur/avatar/25721.jpg")); ?>
    </div>
    <div class="forum-user-panel-about">
        <?= \mpf\web\helpers\Html::get()->link(['user', 'profile', ['name' => \mpf\WebApp::get()->user()->name]], \mpf\WebApp::get()->user()->name, ['class' => 'form-user-panel-about-name']); ?>
        <?= \mpf\web\helpers\Html::get()->link(['user', 'profile', ['name' => \mpf\WebApp::get()->user()->title]], \mpf\WebApp::get()->user()->title, ['class' => 'form-user-panel-about-title']); ?>
    </div>
    <div class="forum-user-panel-links">
        <?= \mpf\web\helpers\Html::get()->link(['home'], 'Categories'); ?>
        <?= \mpf\web\helpers\Html::get()->link(['user', 'controlPanel'], 'Control Panel'); ?>
        <?= \mpf\web\helpers\Html::get()->link(['search', 'recent'], 'Recent Threads'); ?>
        <?= \mpf\web\helpers\Html::get()->link(['members'], 'Members'); ?>
    </div>
</div>