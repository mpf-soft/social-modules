<?php /* @var $this \mpf\modules\forum\components\Controller */ ?>
<?php $url = $this->updateURLWithSection(['search', 'index']) ?>
<div class="forum-search-panel">
    <form method="get" class="forum-search-panel-form" action="<?= $this->getRequest()->createURL($url[0], $url[1], isset($url[2])?$url[2]:[]); ?>">
        <input type="text" name="search" placeholder="<?= \mpf\modules\forum\components\Translator::get()->translate("Search forum.."); ?>" class="forum-search-panel-input" />
        <input type="submit" value="<?= \mpf\modules\forum\components\Translator::get()->translate("Search"); ?>" class="forum-search-panel-button" />
    </form>
</div>

<script>
    $(document).ready(function () {
        $('.forum-search-panel-form').submit(function(){ //can't search with empty form;
            if ($('.forum-search-panel-input', this).val() == "" || $('.forum-search-panel-input', this).val() == "<?= \mpf\modules\forum\components\Translator::get()->translate("Search forum.."); ?>"){
                return false;
            }
        });
    });
</script>