<?php /* @var $this \app\modules\forum\controllers\Home */ ?>
<?php /* @var $categories \app\modules\forum\models\ForumCategory[] */ ?>
<?php $menu = []; ?>
<?php if (\app\modules\forum\components\UserAccess::get()->isSectionAdmin($this->sectionID)) { ?>
    <?php
        $menu = [
            [
                'url' => ['manage', 'groups'],
                'label' => 'Manage Groups'
            ],
            [
                'url' => ['manage', 'categories'],
                'label' => 'Manage Categories'
            ],
            [
                'url' => ['manage', 'users'],
                'label' => 'Manage Users'
            ]
        ];
    ?>
<?php } ?>
<?= \app\components\htmltools\Page::get()->title("Forum", $menu); ?>
<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\app\modules\forum\components\UserAccess::get()->canRead($this->sectionID)) { ?>
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
                <th class="subcategory-threads-column"><?= \app\modules\forum\components\Translator::get()->translate('Threads'); ?></th>
                <th class="subcategory-replies-column"><?= \app\modules\forum\components\Translator::get()->translate('Replies'); ?></th>
                <th class="subcategory-latest-post-column"><?= \app\modules\forum\components\Translator::get()->translate('Latest Post'); ?></th>
            </tr>
            <?php foreach ($category->subcategories as $subcategory) { ?>
                <tr>

                </tr>
            <?php } ?>
        <?php } ?>
    </table>
</div>