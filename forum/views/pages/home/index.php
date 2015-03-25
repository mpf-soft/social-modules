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
        ],
        [
            'url' => $this->updateURLWithSection(['manage', 'users']),
            'label' => 'Manage Users'
        ],
        [
            'url' => $this->updateURLWithSection(['manage', 'titles']),
            'label' => 'Manage Titles'
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
            <tr class="category-title-row">
                <th colspan="4"><?php $this->displayComponent('categorytitle', ['category' => $category]); ?></th>
            </tr>
            <tr class="subcategory-description-row">
                <th class="subcategory-title-column"><?= \app\modules\forum\components\Translator::get()->translate('Subcategory'); ?></th>
                <th class="subcategory-options-column">&nbsp</th>
                <th class="subcategory-activity-column">&nbsp</th>
                <th class="subcategory-latest-post-column"><?= \app\modules\forum\components\Translator::get()->translate('Latest Post'); ?></th>
            </tr>
            <?php foreach ($category->subcategories as $subcategory) { ?>
                <tr class="subcategory-row">
                    <td class="subcategory-title-column">
                        <?= \mpf\web\helpers\Html::get()->link(
                            $this->updateURLWithSection(['subcategory', 'index', ['category' => $category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->id]]),
                            \mpf\web\helpers\Html::get()->image($this->getUploadUrl() . 'subcategories/' . $subcategory->icon),
                            ['class' => 'subcategory-icon']
                        ); ?>
                        <?= \mpf\web\helpers\Html::get()->link(
                            $this->updateURLWithSection(['subcategory', 'index', ['category' => $category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->id]]),
                            $subcategory->title,
                            ['class' => 'subcategory-title']
                        ); ?>
                        <span><?= $subcategory->description; ?></span>
                    </td>
                    <td class="subcategory-options-column">&nbsp;</td>
                    <td class="subcategory-activity-column">
                        <b><?= $subcategory->numberofthreads; ?> <?= \app\modules\forum\components\Translator::get()->translate("threads"); ?></b>
                        <b><?= $subcategory->numberofreplies; ?> <?= \app\modules\forum\components\Translator::get()->translate("replies"); ?></b>
                    </td>
                    <td class="subcategory-latest-post-column">
                            <?php if ($subcategory->last_thread_updated_id) { ?>
                                <?= \mpf\web\helpers\Html::get()->link(
                                    $this->updateURLWithSection(['thread', 'index', ['subcategory' => $subcategory->url_friendly_title, 'category' => $category->url_friendly_name, 'id' => $subcategory->lastThreadUpdated->id]]),
                                    $subcategory->lastThreadUpdated->title,
                                    ['class' => 'subcategory-latest-post-thread']); ?>
                                <span class="subcategory-lastest-post-author">By <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $subcategory->last_active_user_id]]), $subcategory->lastThreadAuthor->name); ?></span>
                                <span class="subcategory-lastest-post-date">, <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($subcategory->last_update_time)); ?></span>
                            <?php } else { ?>
                        <span class="subcategory-no-posts">
                                <?= \app\modules\forum\components\Translator::get()->translate("- no posts -"); ?>
                        </span>
                            <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
</div>