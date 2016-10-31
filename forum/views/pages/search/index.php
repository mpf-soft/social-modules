<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $categories \mpf\modules\forum\models\ForumCategory[] */ ?>
<?php /* @var $threads \mpf\modules\forum\models\ForumThread[] */ ?>
<?php /* @var $numberOfThreads int */ ?>
<?php /* @var $currentPage int */ ?>
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
<?= \app\components\htmltools\Page::get()->title(
    \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home']), \mpf\modules\forum\components\Translator::get()->translate($this->forumTitle)) .
    " " . \mpf\modules\forum\components\Config::value("FORUM_PAGE_TITLE_SEPARATOR") . " " . \mpf\modules\forum\components\Translator::get()->translate("Search") .
    (isset($_GET['search'])?
        ": " . $_GET['search']
        :""
    ), $menu); ?>
<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('searchbar'); ?>
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\mpf\modules\forum\components\UserAccess::get()->canRead($this->sectionId)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'section']); ?>
        <?php return; ?>
    <?php } ?>
    <?php $this->displayComponent("pagelist", [
        'elementsName' => \mpf\modules\forum\components\Translator::get()->translate('threads'),
        'totalElements' => $numberOfThreads,
        'visibleElements' => count($threads),
        'totalPages' => (int)(($numberOfThreads / \mpf\modules\forum\components\Config::value('FORUM_THREADS_PER_PAGE')) + (($numberOfThreads % \mpf\modules\forum\components\Config::value('FORUM_THREADS_PER_PAGE')) ? 1 : 0)),
        'currentPage' => $currentPage
    ]); ?>

    <table class="forum-category">
        <tr class="subcategory-title-row">
            <th colspan="6">
                <h2>
                    <a class="subcategory-icon" href="#">
                        <?= \mpf\web\helpers\Html::get()->mpfImage('oxygen/48x48/actions/edit-web-search.png'); ?>
                    </a>
                    <a class="subcategory-title" href="#"><?= \mpf\modules\forum\components\Translator::get()->translate('Search results'); ?></a>
                    <span><?= \mpf\modules\forum\components\Translator::get()->translate('Showing results for last search'); ?></span>
                </h2>
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
        <?php if (!$threads) { ?>
            <tr class="no-threads-found-row">
                <td colspan="6"><?= \mpf\modules\forum\components\Translator::get()->translate("No Threads Found Yet!"); ?></td>
            </tr>
        <?php } else { ?>
            <?php foreach ($threads as $thread) { ?>
                <tr class="thread-row">
                    <td class="thread-status-icon-column"><?= \mpf\web\helpers\Html::get()->image($this->getWebRoot() . 'forum/statusicons/' . $thread->getStatus() . '.png', ucfirst($thread->getStatus())) ?></td>
                    <td class="thread-title-column">
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['subcategory' => $thread->subcategory->url_friendly_title, 'category' => $thread->category->url_friendly_name, 'id' => $thread->id]]), $thread->title); ?>
                    </td>
                    <td class="thread-started-by-column">
                        <?= $thread->getOwnerProfileLink(); ?>
                        <span><?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->create_time, false ,false); ?></span>
                    </td>
                    <td class="thread-replies-column"><?= $thread->replies; ?></td>
                    <td class="thread-views-column"><?= $thread->views; ?></td>
                    <td class="thread-most-recent-column">
                        <?php if ($thread->last_reply_id) { ?>
                            <?= $thread->getLastActiveProfileLink(); ?>
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
    </table>
    <?php $this->displayComponent("pagelist", [
        'elementsName' => \mpf\modules\forum\components\Translator::get()->translate('threads'),
        'totalElements' => $numberOfThreads,
        'visibleElements' => count($threads),
        'totalPages' => (int)(($numberOfThreads / \mpf\modules\forum\components\Config::value('FORUM_THREADS_PER_PAGE')) + (($numberOfThreads % \mpf\modules\forum\components\Config::value('FORUM_THREADS_PER_PAGE')) ? 1 : 0)),
        'currentPage' => $currentPage
    ]); ?>

</div>