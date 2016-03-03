<div class="accessdenied-reason">
    <?php if ('category' == $location) { ?>
        <?= \mpf\modules\forum\components\Translator::get()->translate("Can't access this category!"); ?>
    <?php } elseif ('userprofile' == $location) { ?>
        <?php if (\mpf\WebApp::get()->user()->isGuest()) { ?>
            <?= \mpf\modules\forum\components\Translator::get()->translate("Must login to see user profile!"); ?>
        <?php } else { ?>
            <?= \mpf\modules\forum\components\Translator::get()->translate("Can't access user profile!"); ?>
        <?php } ?>
    <?php } else { ?>
        <?= \mpf\modules\forum\components\Translator::get()->translate("Access denied"); ?>
    <?php } ?>
</div>
