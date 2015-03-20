<?php /* @var $this \app\modules\forum\controllers\Home */ ?>
<?php /* @var $categories \app\modules\forum\models\ForumCategory[] */ ?>
<?php $menu = []; ?>
<?php if (\app\modules\forum\components\UserAccess::get()->isSectionAdmin($this->sectionId)) { ?>
    <?php
        $menu = [
            [
                'url' => $this->updateURLWithSection(['manage', 'groups']),
                'label' => 'Manage Groups'
            ],
            [
                'url' => $this->updateURLWithSection(['manage', 'categories']),
                'label' => 'Manage Categories'
            ]
        ];
    ?>
<?php } ?>
<?= \app\components\htmltools\Page::get()->title("Forum", $menu); ?>
<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\app\modules\forum\components\UserAccess::get()->canRead($this->sectionId)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'section']); ?>
        <?php return; ?>
    <?php } ?>

    <table class="forum-section">
        <?php foreach ($categories as $category) { ?>
            <tr>
                <th colspan="5"><?php $this->displayComponent('categorytitle', ['category' => $category]); ?></th>
            </tr>
            <tr>
                <th class="subcategory-title-column"><?= \app\modules\forum\components\Translator::get()->translate('Subcategory'); ?></th>
                <th class="subcategory-options-column">&nbsp</th>
                <th class="subcategory-activity-column">&nbsp</th>
                <th class="subcategory-latest-post-column"><?= \app\modules\forum\components\Translator::get()->translate('Latest Post'); ?></th>
            </tr>
            <?php foreach ($category->subcategories as $subcategory) { ?>
                <tr>

                </tr>
            <?php } ?>
        <?php } ?>
    </table>
</div>