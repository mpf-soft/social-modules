<?php /* @var $this \app\modules\forum\controllers\Home */ ?>
<?php /* @var $subcategory \app\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $thread \app\modules\forum\models\ForumThread */ ?>
<?php /* @var $replies \app\modules\forum\models\ForumReply[] */ ?>
<?php /* @var $replyModel \app\modules\forum\models\ForumReply */ ?>
<?php /* @var $currentPage int */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $subcategory->category->url_friendly_name, 'id' => $subcategory->category_id]]), $subcategory->category->name)
    . " " . $this->pageTitleSeparator . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $subcategory->category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->category_id]]), $subcategory->title)
    . " " . $this->pageTitleSeparator . " "
    . $thread->title); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\app\modules\forum\components\UserAccess::get()->canRead($this->sectionId)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'subcategory']); ?>
        <?php return; ?>
    <?php } ?>

    <?php $this->displayComponent("pagelist", [
        'elementsName' => \app\modules\forum\components\Translator::get()->translate('replies'),
        'totalElements' => $thread->replies,
        'visibleElements' => count($replies),
        'totalPages' => (int)(($thread->replies / $this->repliesPerPage) + (($thread->replies % $this->repliesPerPage) ? 1 : 0)),
        'currentPage' => $currentPage
    ]); ?>

    <table class="forum-thread <?= $currentPage == 1 ? "first-page" : ""; ?>">
        <tr>
            <th colspan="2">
                <h2 class="forum-thread-title">
                    <?= \mpf\web\helpers\Html::get()->image("https://robertsspaceindustries.com/media/7psqknsdcm40ur/avatar/25721.jpg"); ?>
                    <span class="forum-thread-title">
                        <?php if ($thread->sticky) { ?>
                            <?= \mpf\web\helpers\Html::get()->image($this->getWebRoot() . 'forum/statusicons/sticky.png', "Sticky"); ?>
                        <?php } ?>
                        <?php if ($thread->closed) { ?>
                            <?= \mpf\web\helpers\Html::get()->image($this->getWebRoot() . 'forum/statusicons/closed.png', "Closed"); ?>
                        <?php } ?>
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'index', ['subcategory' => $subcategory->url_friendly_title, 'category' => $subcategory->category->url_friendly_name, 'id' => $thread->id]]), $thread->title); ?>
                    </span>
                    <span class="forum-author-info">
                        <?= \app\modules\forum\components\Translator::get()->translate("Started by"); ?>
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->user_id]]), $thread->owner->name); ?>
                        <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->create_time, false, false)); ?>
                    </span>

                    <div class="forum-thread-title-actions">
                        <?php if (\app\modules\forum\components\UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)) { ?>
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'move', ['id' => $thread->id]]), \app\modules\forum\components\Translator::get()->translate('Move'), ['class' => 'move-thread-button']); ?>
                            <?php if ($thread->closed) { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'close', ['id' => $thread->id]]), \app\modules\forum\components\Translator::get()->translate('Open'), ['class' => 'open-thread-button']); ?>
                            <?php } else { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'close', ['id' => $thread->id]]), \app\modules\forum\components\Translator::get()->translate('Close'), ['class' => 'close-thread-button']); ?>
                            <?php } ?>
                            <?php if ($thread->sticky) { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'sticky', ['id' => $thread->id]]), \app\modules\forum\components\Translator::get()->translate('Not Sticky'), ['class' => 'not-sticky-thread-button']); ?>
                            <?php } else { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'sticky', ['id' => $thread->id]]), \app\modules\forum\components\Translator::get()->translate('Sticky'), ['class' => 'sticky-thread-button']); ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if (\app\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
                            <?= \mpf\web\helpers\Html::get()->link('#reply-form', \app\modules\forum\components\Translator::get()->translate('Reply'), ['class' => 'new-reply-button']); ?>
                        <?php } ?>
                    </div>
                </h2>
            </th>
        </tr>
        <tr class="forum-reply forum-main-post <?= $thread->getSectionUser($subcategory->category->section_id)->group->html_class; ?>">
            <td class="forum-user-details">
                <b class="forum-user-details-name">
                    <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->user_id]]), $thread->owner->name); ?>
                </b>
                    <span class="forum-user-details-title">
                        <?= $thread->getSectionUser($subcategory->category->section_id)->title->title; ?>
                    </span>
                <?= \mpf\web\helpers\Html::get()->image("https://robertsspaceindustries.com/media/7psqknsdcm40ur/avatar/25721.jpg"); ?>
                <span class="forum-user-details-group">
                        <?= $thread->getSectionUser($subcategory->category->section_id)->group->full_name; ?>
                </span>
                <span class="forum-user-details-date">
                    <?= \app\modules\forum\components\Translator::get()->translate("Member since"); ?>
                    <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->getSectionUser($subcategory->category->section_id)->member_since, false, false)); ?>
                </span>
            </td>
            <td class="forum-reply-content">
                <?php if ($thread->canEdit($subcategory->category_id, $subcategory->category->section_id)) { ?>
                    <div class="forum-reply-managment-links">
                        <?= \mpf\web\helpers\Html::get()->link(
                            $this->updateURLWithSection(['thread', 'edit', ['id' => $thread->id]]),
                            \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/story-editor.png", "Edit post")
                        ); ?>
                        <?= \mpf\web\helpers\Html::get()->postLink(
                            $this->updateURLWithSection(['thread', 'deleteThread']),
                            \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/dialog-cancel.png", "Delete Thread"),
                            ['id' => $thread->id], [],
                            false,
                            \app\modules\forum\components\Translator::get()->translate("Are you sure you want to delete this reply? You can't undo this action!")
                        ); ?>
                    </div>
                <?php } ?>
                <div
                    class="forum-reply-content-date"><?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->create_time); ?></div>
                <?= $thread->getContent(); ?>
                <?php if ($thread->edit_user_id) { ?>
                    <div class="forum-reply-edit-details">
                        <?php if ($thread->edit_user_id == $thread->user_id) { ?>
                            <?= \app\modules\forum\components\Translator::get()->translate("Edited"); ?>
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->edit_time, false, false); ?>
                        <?php } else { ?>
                            <?= \app\modules\forum\components\Translator::get()->translate("Edited by"); ?>
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->edit_user_id]]), $thread->editor->name); ?>
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->edit_time, false, false); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?= $this->threadSignatureSeparator; ?>
                <?= $thread->getSectionUser($subcategory->category->section_id)->getSignature(); ?>
            </td>
        </tr>
        <?php foreach ($replies as $reply) { ?>
            <tr class="forum-reply  <?= $reply->getSectionUser($subcategory->category->section_id)->group->html_class; ?>">
                <td class="forum-user-details">
                    <b class="forum-user-details-name">
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $reply->user_id]]), $reply->author->name); ?>
                    </b>
                    <span class="forum-user-details-title">
                        <?= $reply->getSectionUser($subcategory->category->section_id)->title->title; ?>
                    </span>
                    <?= \mpf\web\helpers\Html::get()->image("https://robertsspaceindustries.com/media/7psqknsdcm40ur/avatar/25721.jpg"); ?>
                    <span class="forum-user-details-group">
                        <?= $reply->getSectionUser($subcategory->category->section_id)->group->full_name; ?>
                    </span>
                <span class="forum-user-details-date">
                    <?= \app\modules\forum\components\Translator::get()->translate("Member since"); ?>
                    <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($reply->getSectionUser($subcategory->category->section_id)->member_since, false, false)); ?>
                </span>
                </td>
                <td class="forum-reply-content">
                    <?php if ($reply->canEdit($subcategory->category_id, $subcategory->category->section_id)) { ?>
                        <div class="forum-reply-managment-links">
                            <?= \mpf\web\helpers\Html::get()->link(
                                $this->updateURLWithSection(['thread', 'editReply', ['id' => $reply->id]]),
                                \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/story-editor.png", "Edit reply")
                            ); ?>
                            <?= \mpf\web\helpers\Html::get()->postLink(
                                $this->updateURLWithSection(['thread', 'deleteReply']),
                                \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/dialog-cancel.png", "Delete reply"),
                                ['id' => $reply->id], [],
                                false,
                                \app\modules\forum\components\Translator::get()->translate("Are you sure you want to delete this reply? You can`t undo this action!")
                            ); ?>
                        </div>
                    <?php } ?>
                    <div
                        class="forum-reply-content-date"><?= \mpf\helpers\DateTimeHelper::get()->niceDate($reply->time); ?></div>
                    <?= $reply->getContent(); ?>
                    <?php if ($reply->edited) { ?>
                        <div class="forum-reply-edit-details">
                            <?php if ($reply->edit_user_id == $reply->user_id) { ?>
                                <?= \app\modules\forum\components\Translator::get()->translate("Edited"); ?>
                                <?= \mpf\helpers\DateTimeHelper::get()->niceDate($reply->edit_time, false, false); ?>
                            <?php } else { ?>
                                <?= \app\modules\forum\components\Translator::get()->translate("Edited by"); ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $reply->edit_user_id]]), $reply->editor->name); ?>
                                <?= \mpf\helpers\DateTimeHelper::get()->niceDate($reply->edit_time, false, false); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?= $this->threadSignatureSeparator; ?>
                    <?= $reply->getSectionUser($subcategory->category->section_id)->getSignature(); ?>
                </td>
            </tr>
        <?php } ?>
        <?php if (!$thread->closed && \app\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
            <tr class="forum-reply-form">
                <td class="forum-user-details"><a name="reply-form"></a>
                    <b class="forum-user-details-name">
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => \mpf\WebApp::get()->user()->id]]), \mpf\WebApp::get()->user()->name); ?>
                    </b>
                    <span class="forum-user-details-title">
                        <?= ($t = \app\modules\forum\components\UserAccess::get()->getUserTitle($subcategory->category->section_id)) ? $t->title : '-'; ?>
                    </span>
                    <?= \mpf\web\helpers\Html::get()->image("https://robertsspaceindustries.com/media/7psqknsdcm40ur/avatar/25721.jpg"); ?>
                    <span class="forum-user-details-group">
                    <?= ($g = \app\modules\forum\components\UserAccess::get()->getUserGroup($subcategory->category->section_id)) ? $g->full_name : '-'; ?>
                </span>
                <span class="forum-user-details-date">
                    <?= \app\modules\forum\components\Translator::get()->translate("Member since"); ?>
                    <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->getSectionUser($subcategory->category->section_id)->member_since, false, false)); ?>
                </span>

                </td>
                <td class="forum-reply-form-column">
                    <?php $this->displayComponent("replyform", ['model' => $replyModel]); ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $this->displayComponent("pagelist", [
        'elementsName' => \app\modules\forum\components\Translator::get()->translate('replies'),
        'totalElements' => $thread->replies,
        'visibleElements' => count($replies),
        'totalPages' => (int)(($thread->replies / $this->threadsPerPage) + (($thread->replies % $this->repliesPerPage) ? 1 : 0)),
        'currentPage' => $currentPage
    ]); ?>

</div>

