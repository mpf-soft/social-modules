<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $category \mpf\modules\forum\models\ForumCategory */ ?>
<?php $menu = []; ?>
<?php if (\mpf\modules\forum\components\UserAccess::get()->isSectionAdmin($this->sectionId)) { ?>
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
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum") . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " " . $category->name, $menu); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\mpf\modules\forum\components\UserAccess::get()->canRead($this->sectionId, $category->id)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'category']); ?>
        <?php return; ?>
    <?php } ?>

    <table class="forum-category">
        <?php foreach ($category->subcategories as $subcategory) { ?>
            <tr class="subcategory-title-row">
                <th colspan="6">
                    <h2>
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
                    </h2>
                    <?php if (\mpf\modules\forum\components\UserAccess::get()->canCreateNewThread($category->id, $this->sectionId)) { ?>
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'new', ['subcategory' => $subcategory->id]]), \mpf\modules\forum\components\Translator::get()->translate('New Thread'), ['class' => 'new-thread-button']); ?>
                    <?php } ?>
                </th>
            </tr>
            <tr class="threads-description-row">
                <th class="thread-status-icon-column">&nbsp;</th>
                <th class="thread-title-column"><?= \mpf\modules\forum\components\Translator::get()->translate("Thread"); ?></th>
                <th class="thread-started-by-column"><?= \mpf\modules\forum\components\Translator::get()->translate("Started By"); ?></th>
                <th class="thread-replies-column"><?= \mpf\modules\forum\components\Translator::get()->translate("Replies"); ?></th>
                <th class="thread-views-column"><?= \mpf\modules\forum\components\Translator::get()->translate("Views"); ?></th>
                <th class="thread-most-recent-column"><?= \mpf\modules\forum\components\Translator::get()->translate("Most Recent"); ?></th>
            </tr>
            <?php if (!$subcategory->number_of_threads) { ?>
                <tr class="no-threads-found-row">
                    <td colspan="6"><?= \mpf\modules\forum\components\Translator::get()->translate("No Threads Found Yet!"); ?></td>
                </tr>
            <?php } else { ?>
                <?php foreach ($subcategory->getTopPostsForCategoryPage() as $thread) { ?>
                    <tr class="thread-row">
                        <td class="thread-status-icon-column"><?= \mpf\web\helpers\Html::get()->image($this->getWebRoot() .'forum/statusicons/' . $thread->getStatus() . '.png', ucfirst($thread->getStatus())) ?></td>
                        <td class="thread-title-column">
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['subcategory' => $subcategory->url_friendly_title, 'category' => $subcategory->category->url_friendly_name, 'id' => $thread->id]]), $thread->title); ?>
                        </td>
                        <td class="thread-started-by-column">
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->user_id]]), $thread->owner->name); ?>
                            <span><?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->create_time, false ,false); ?></span>
                        </td>
                        <td class="thread-replies-column"><?= $thread->replies; ?></td>
                        <td class="thread-views-column"><?= $thread->views; ?></td>
                        <td class="thread-most-recent-column">
                            <?php if ($thread->last_reply_id) { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->last_reply_user_id, 'name' => $thread->lastActiveUser->name]]), $thread->lastActiveUser->name); ?>
                                <span><?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->last_reply_date, false ,false); ?></span>
                            <?php } else { ?>
                                <span class="thread-no-replies-message">
                                    <?= \mpf\modules\forum\components\Translator::get()->translate("no replies"); ?>
                                </span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </table>

    <div class="forum-section-footer">
        <?php if (\mpf\modules\forum\components\UserAccess::get()->isCategoryModerator($category->id, $this->sectionId)) { ?>
            <div class="forum-section-actions">
                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['manage', 'editCategory', ['id' => $category->id]]),
                    \mpf\modules\forum\components\Translator::get()->translate("Edit Category"));
                ?>
                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['manage', 'subcategories', ['category' => $category->id]]),
                    \mpf\modules\forum\components\Translator::get()->translate("Manage Subcategorie"));
                ?>
            </div>
        <?php } ?>

        <div class="forum-section-jump-to-category">
            <select id="jump-to-category">
                <option><?= \mpf\modules\forum\components\Translator::get()->translate("--jump to category--"); ?></option>
                <?php foreach ($categories as $category) { ?>
                    <optgroup label="<?= $category->name; ?>">
                        <?php foreach ($category->subcategories as $subcategory) { ?>
                            <option value="<?= $subcategory->id; ?>"><?= $subcategory->title; ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<script>
    var Categories2URLs = [];

    <?php foreach ($categories as $category) { ?>
    <?php foreach ($category->subcategories as $subcategory) { ?>
    <?php $url = $this->updateURLWithSection(['subcategory', 'index', ['category' => $category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->id]]); ?>
    Categories2URLs[<?= $subcategory->id; ?>] = "<?= $this->getRequest()->createURL($url[0], $url[1], $url[2]); ?>";
    <?php } ?>
    <?php } ?>

    $(document).ready(function(){
        $('#jump-to-category').change(function(){
            window.location = Categories2URLs[$(this).val()];
        });
    })
</script>