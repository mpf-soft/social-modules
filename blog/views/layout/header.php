<?php if (\mpf\modules\blog\components\BlogConfig::get()->customHeader) { ?>
    <?php require_once \mpf\modules\blog\components\BlogConfig::get()->customHeader; ?>
    <?php return; ?>
<?php } ?>
<?= \app\components\htmltools\Page::get()->title($this->blogTitle . ($this->subTitle ? ' - ' . $this->subTitle : '')); ?>
<div id="blog-page">
    <div id="main-blog-section">
        
    