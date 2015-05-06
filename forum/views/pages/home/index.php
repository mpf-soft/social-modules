<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $categories \mpf\modules\forum\models\ForumCategory[] */ ?>
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
<?php } elseif (\mpf\modules\forum\components\UserAccess::get()->isSectionModerator($this->sectionId)) { ?>
    <?php
    $menu = [
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
    <?php $this->displayComponent('searchbar'); ?>
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\mpf\modules\forum\components\UserAccess::get()->canRead($this->sectionId)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'section']); ?>
        <?php return; ?>
    <?php } ?>

    <table class="forum-section forum-hide-hidden">
        <?php foreach ($categories as $category) { ?>
            <tr class="category-title-row">
                <th colspan="4"><?php $this->displayComponent('categorytitle', ['category' => $category]); ?></th>
            </tr>
            <tr class="subcategory-description-row">
                <th class="subcategory-title-column"><?= \mpf\modules\forum\components\Translator::get()->translate('Subcategory'); ?></th>
                <th class="subcategory-options-column">&nbsp</th>
                <th class="subcategory-activity-column">&nbsp</th>
                <th class="subcategory-latest-post-column"><?= \mpf\modules\forum\components\Translator::get()->translate('Latest Post'); ?></th>
            </tr>
            <?php foreach ($category->subcategories as $subcategory) { ?>
                <tr class="subcategory-row <?= $subcategory->hidden?'hidden-subcategory':''; ?>">
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
                    <td class="subcategory-options-column">
                        <?php if ($subcategory->hidden) { ?>
                            <?= \mpf\web\helpers\Html::get()->postLink(
                                $this->updateURLWithSection(['home', 'action']),
                                \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/emotes/opinion-okay.png", "Show category"),
                                [
                                    "action" => "show_subcategory",
                                    "id" => $subcategory->id
                                ]
                            );
                            ?>
                        <?php } else { ?>
                            <?= \mpf\web\helpers\Html::get()->postLink(
                                $this->updateURLWithSection(['home', 'action']),
                                \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/emotes/opinion-no.png", "Hide category"),
                                [
                                    "action" => "hide_subcategory",
                                    "id" => $subcategory->id
                                ]
                            );
                            ?>
                        <?php } ?>
                    </td>
                    <td class="subcategory-activity-column">
                        <b><?= $subcategory->number_of_threads; ?> <?= \mpf\modules\forum\components\Translator::get()->translate("threads"); ?></b>
                        <b><?= $subcategory->number_of_replies; ?> <?= \mpf\modules\forum\components\Translator::get()->translate("replies"); ?></b>
                    </td>
                    <td class="subcategory-latest-post-column">
                        <?php if ($subcategory->last_active_thread_id) { ?>
                            <?= \mpf\web\helpers\Html::get()->link(
                                $this->updateURLWithSection(['thread', 'index', ['subcategory' => $subcategory->url_friendly_title, 'category' => $category->url_friendly_name, 'id' => $subcategory->last_active_thread_id]]),
                                $subcategory->lastActiveThread->title,
                                ['class' => 'subcategory-latest-post-thread']); ?>
                            <span
                                class="subcategory-lastest-post-author"><?= $subcategory->getActionForList(); ?> <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $subcategory->last_active_user_id, 'name' => $subcategory->lastActiveUser->name]]), $subcategory->lastActiveUser->name); ?></span>
                            <span
                                class="subcategory-lastest-post-date">, <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($subcategory->last_activity_time, false, false)); ?></span>
                        <?php } else { ?>
                            <span class="subcategory-no-posts">
                                <?= \mpf\modules\forum\components\Translator::get()->translate("- no posts -"); ?>
                        </span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
    <div class="forum-section-footer">
        <div class="forum-section-actions">
            <a href="#" onclick="return showHideHiddenSubcategories(this);"><?= \mpf\modules\forum\components\Translator::get()->translate("Show Hidden Subcategories"); ?></a>
        </div>
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

    function showHideHiddenSubcategories(button){
        if ($('.forum-section').hasClass('forum-hide-hidden')) {
            $('.forum-section').removeClass('forum-hide-hidden');
            $(button).text('<?= \mpf\modules\forum\components\Translator::get()->translate("Hide Hidden Subcategories"); ?>');
        } else {
            console.log("doesn't have class");
            $('.forum-section').addClass('forum-hide-hidden');
            $(button).text('<?= \mpf\modules\forum\components\Translator::get()->translate("Show Hidden Subcategories"); ?>');
        }
        return false;
    }

    $(document).ready(function(){
        $('#jump-to-category').change(function(){
            window.location = Categories2URLs[$(this).val()];
        });
    })
</script>