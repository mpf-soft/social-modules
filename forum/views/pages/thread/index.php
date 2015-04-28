<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $subcategory \mpf\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $thread \mpf\modules\forum\models\ForumThread */ ?>
<?php /* @var $replies \mpf\modules\forum\models\ForumReply[] */ ?>
<?php /* @var $replyModel \mpf\modules\forum\models\ForumReply */ ?>
<?php /* @var $currentPage int */ ?>
<?= \app\components\htmltools\Page::get()->title(\mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['home', 'index']), "Forum")
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['category', 'index', ['category' => $subcategory->category->url_friendly_name, 'id' => $subcategory->category_id]]), $subcategory->category->name)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['subcategory', 'index', ['category' => $subcategory->category->url_friendly_name, 'subcategory' => $subcategory->url_friendly_title, 'id' => $subcategory->id]]), $subcategory->title)
    . " " . \mpf\modules\forum\components\Config::value('FORUM_PAGE_TITLE_SEPARATOR') . " "
    . $thread->title); ?>

<div class="forum-page <?= $this->forumPageTheme; ?>">
    <?php $this->displayComponent('topuserpanel'); ?>

    <?php if (!\mpf\modules\forum\components\UserAccess::get()->canRead($this->sectionId)) { ?>
        <?php $this->displayComponent('accessdenied', ['location' => 'subcategory']); ?>
        <?php return; ?>
    <?php } ?>



    <?php $this->displayComponent("pagelist", [
        'elementsName' => \mpf\modules\forum\components\Translator::get()->translate('replies'),
        'totalElements' => $thread->first_level_replies . ' ( ' . $thread->replies . ' )',
        'visibleElements' => count($replies),
        'totalPages' => (int)(($thread->first_level_replies / \mpf\modules\forum\components\Config::value('FORUM_REPLIES_PER_PAGE')) + (($thread->first_level_replies % \mpf\modules\forum\components\Config::value('FORUM_REPLIES_PER_PAGE')) ? 1 : 0)),
        'currentPage' => $currentPage
    ]); ?>

    <table class="forum-thread <?= $currentPage == 1 ? "first-page" : ""; ?>">
        <?php if ($thread->closed) { ?>
            <tr class="forum-thread-closed">
                <th colspan="2">
                    <i><?= \mpf\modules\forum\components\Translator::get()->translate("This thread is closed! Only moderators can add or edit replies!"); ?></i>
                </th>
            </tr>
        <?php } ?>
        <tr>
            <th colspan="2">
                <h2 class="forum-thread-title">
                    <?= \mpf\web\helpers\Html::get()->image($thread->getAuthorIcon()); ?>
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
                        <?= \mpf\modules\forum\components\Translator::get()->translate("Started by"); ?>
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->user_id]]), $thread->owner->name); ?>
                        <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->create_time, false, false)); ?>
                    </span>

                    <div class="forum-thread-title-actions">
                        <?php if (\mpf\modules\forum\components\UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)) { ?>
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'move', ['id' => $thread->id]]), \mpf\modules\forum\components\Translator::get()->translate('Move'), ['class' => 'move-thread-button']); ?>
                            <?php if ($thread->closed) { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'close', ['id' => $thread->id]]), \mpf\modules\forum\components\Translator::get()->translate('Open'), ['class' => 'open-thread-button']); ?>
                            <?php } else { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'close', ['id' => $thread->id]]), \mpf\modules\forum\components\Translator::get()->translate('Close'), ['class' => 'close-thread-button']); ?>
                            <?php } ?>
                            <?php if ($thread->sticky) { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'sticky', ['id' => $thread->id]]), \mpf\modules\forum\components\Translator::get()->translate('Not Sticky'), ['class' => 'not-sticky-thread-button']); ?>
                            <?php } else { ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['thread', 'sticky', ['id' => $thread->id]]), \mpf\modules\forum\components\Translator::get()->translate('Sticky'), ['class' => 'sticky-thread-button']); ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if (\mpf\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
                            <?= \mpf\web\helpers\Html::get()->link('#reply-form', \mpf\modules\forum\components\Translator::get()->translate('Reply'), ['class' => 'new-reply-button']); ?>
                        <?php } ?>
                    </div>
                </h2>
            </th>
        </tr>
        <tr class="forum-reply forum-main-post forum-group-class-<?= $thread->getSectionUser($subcategory->category->section_id)->group->html_class; ?>">
            <td class="forum-user-details">
                <div class="forum-user-details-header">
                    <b class="forum-user-details-name">
                        <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->user_id, 'name' => $thread->owner->name]]), $thread->owner->name); ?>
                    </b>
                    <span class="forum-user-details-title">
                        <?= $thread->getSectionUser($subcategory->category->section_id)->title->title; ?>
                    </span>
                </div>
                <?= \mpf\web\helpers\Html::get()->image($thread->getAuthorIcon()); ?>
                <div class="forum-user-details-footer">
                    <span class="forum-user-details-group">
                            <?= $thread->getSectionUser($subcategory->category->section_id)->group->full_name; ?>
                    </span>
                    <span class="forum-user-details-date">
                        <?= \mpf\modules\forum\components\Translator::get()->translate("Member since"); ?>
                        <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->getSectionUser($subcategory->category->section_id)->member_since, false, false)); ?>
                    </span>
                </div>
            </td>
            <td class="forum-reply-content">
                <?php if ($thread->canEdit($subcategory->category_id, $subcategory->category->section_id, $thread)) { ?>
                    <div class="forum-reply-management-links">
                        <?= \mpf\web\helpers\Html::get()->link(
                            $this->updateURLWithSection(['thread', 'edit', ['id' => $thread->id]]),
                            \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/story-editor.png", "Edit post")
                        ); ?>
                        <?= \mpf\web\helpers\Html::get()->postLink(
                            $this->updateURLWithSection(['thread', 'deleteThread']),
                            \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/dialog-cancel.png", "Delete Thread"),
                            ['id' => $thread->id], [],
                            false,
                            \mpf\modules\forum\components\Translator::get()->translate("Are you sure you want to delete this reply? You can't undo this action!")
                        ); ?>
                    </div>
                <?php } ?>
                <div
                    class="forum-reply-content-date"><?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->create_time); ?></div>
                <?= $thread->getContent(); ?>
                <?php if ($thread->edit_user_id) { ?>
                    <div class="forum-reply-edit-details">
                        <?php if ($thread->edit_user_id == $thread->user_id) { ?>
                            <?= \mpf\modules\forum\components\Translator::get()->translate("Edited"); ?>
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->edit_time, false, false); ?>
                        <?php } else { ?>
                            <?= \mpf\modules\forum\components\Translator::get()->translate("Edited by"); ?>
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $thread->edit_user_id]]), $thread->editor->name); ?>
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($thread->edit_time, false, false); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?= \mpf\modules\forum\components\Config::value('FORUM_THREAD_SIGNATURE_SEPARATOR'); ?>
                <?= $thread->getSectionUser($subcategory->category->section_id)->getSignature(); ?>
            </td>
        </tr>
        <?php foreach ($replies as $reply) { ?>
            <tr class="forum-reply  forum-group-class-<?= $reply->authorGroup->html_class; ?>">
                <td class="forum-user-details">
                    <a style="visibility: hidden;" name="reply<?= $reply->id; ?>"></a>

                    <div class="forum-user-details-header">
                        <b class="forum-user-details-name">
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $reply->user_id, 'name' => $reply->author->name]]), $reply->author->name); ?>
                        </b>
                        <span class="forum-user-details-title">
                            <?= $reply->sectionAuthor->title->title; ?>
                        </span>
                    </div>
                    <?= \mpf\web\helpers\Html::get()->image($reply->getAuthorIcon()); ?>
                    <div class="forum-user-details-footer">
                        <span class="forum-user-details-group">
                            <?= $reply->authorGroup->full_name; ?>
                        </span>
                        <span class="forum-user-details-date">
                            <?= \mpf\modules\forum\components\Translator::get()->translate("Member since"); ?>
                            <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($reply->sectionAuthor->member_since, false, false)); ?>
                        </span>
                    </div>
                </td>
                <td class="forum-reply-content">
                    <div class="forum-reply-content-header">
                        <div class="forum-reply-management-links">
                            <?php if (\mpf\WebApp::get()->user()->isConnected()) { ?>
                                <?= \mpf\web\helpers\Html::get()->ajaxLink(
                                    $this->updateURLWithSection(['thread', 'vote']),
                                    \mpf\web\helpers\Html::get()->image(\mpf\modules\forum\components\Config::value('FORUM_VOTE_AGREE_ICON'), "Agree"),
                                    'afterReplyVote',
                                    ['id' => $reply->id, 'type' => 'agree', 'level' => 1]
                                ); ?>
                                <?= \mpf\web\helpers\Html::get()->ajaxLink(
                                    $this->updateURLWithSection(['thread', 'vote']),
                                    \mpf\web\helpers\Html::get()->image(\mpf\modules\forum\components\Config::value('FORUM_VOTE_DISAGREE_ICON'), "Disagree"),
                                    'afterReplyVote',
                                    ['id' => $reply->id, 'type' => 'disagree', 'level' => 1]
                                ); ?>
                            <?php } ?>
                            <?= \mpf\web\helpers\Html::get()->link('#reply' . $reply->id,
                                \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/status/mail-attachment.png", "Perma link")
                            ); ?>
                            <?php if ($reply->canEdit($subcategory->category_id, $subcategory->category->section_id, $thread)) { ?>
                                <?= \mpf\web\helpers\Html::get()->link(
                                    $this->updateURLWithSection(['thread', 'editReply', ['id' => $reply->id, 'level' => 1]]),
                                    \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/story-editor.png", "Edit reply")
                                ); ?>
                                <?= \mpf\web\helpers\Html::get()->postLink(
                                    $this->updateURLWithSection(['thread', 'deleteReply']),
                                    \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/dialog-cancel.png", "Delete reply"),
                                    ['id' => $reply->id, 'level' => 1], [],
                                    false,
                                    \mpf\modules\forum\components\Translator::get()->translate("Are you sure you want to delete this reply? You can`t undo this action!")
                                ); ?>
                            <?php } ?>
                        </div>
                        <div class="forum-reply-content-date">
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($reply->time); ?>&nbsp;&nbsp;&nbsp;
                            <span id="number-of-points-for-reply-1-<?= $reply->id; ?>">
                            <?= $reply->score . \mpf\modules\forum\components\Translator::get()->translate(" points"); ?>
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </div>
                    </div>
                    <?= $reply->getContent(); ?>
                    <?php if ($reply->edited && !$reply->deleted) { ?>
                        <div class="forum-reply-edit-details">
                            <?php if ($reply->edit_user_id == $reply->user_id) { ?>
                                <?= \mpf\modules\forum\components\Translator::get()->translate("Edited"); ?>
                                <?= \mpf\helpers\DateTimeHelper::get()->niceDate($reply->edit_time, false, false); ?>
                            <?php } else { ?>
                                <?= \mpf\modules\forum\components\Translator::get()->translate("Edited by"); ?>
                                <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $reply->edit_user_id]]), $reply->editor->name); ?>
                                <?= \mpf\helpers\DateTimeHelper::get()->niceDate($reply->edit_time, false, false); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (!$reply->deleted && \mpf\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
                        <div class="forum-thread-reply-actions">
                            <?= \mpf\web\helpers\Html::get()->link('#reply-for-1-' . $reply->id, \mpf\modules\forum\components\Translator::get()->translate('Reply'), ['class' => 'new-reply-button reply-to-existing-reply']); ?>
                        </div>
                    <?php } ?>
                    <?php if ($reply->sectionAuthor->getSignature()) { ?>
                        <?= \mpf\modules\forum\components\Config::value('FORUM_THREAD_SIGNATURE_SEPARATOR'); ?>
                        <?= $reply->sectionAuthor->getSignature(); ?>
                    <?php } ?>
                    <?php $this->display("_replies", ['reply' => $reply, 'level' => 2]); ?>
                </td>
            </tr>
        <?php } ?>
        <?php if ((!$thread->closed || \mpf\modules\forum\components\UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)) && \mpf\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
            <tr class="forum-reply-form">
                <td class="forum-user-details"><a name="reply-form"></a>
                    <div class="forum-user-details-header">
                        <b class="forum-user-details-name">
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => \mpf\WebApp::get()->user()->id, 'name' => \mpf\WebApp::get()->user()->name]]), \mpf\WebApp::get()->user()->name); ?>
                        </b>
                        <span class="forum-user-details-title">
                            <?= ($t = \mpf\modules\forum\components\UserAccess::get()->getUserTitle($subcategory->category->section_id)) ? $t->title : '-'; ?>
                        </span>
                    </div>
                    <?= \mpf\web\helpers\Html::get()->image(\mpf\modules\forum\components\Config::value('USER_ICON_FOLDER_URL') . (\mpf\WebApp::get()->user()->icon ?: 'default.png')); ?>
                    <div class="forum-user-details-footer">
                        <span class="forum-user-details-group">
                        <?= ($g = \mpf\modules\forum\components\UserAccess::get()->getUserGroup($subcategory->category->section_id)) ? $g->full_name : '-'; ?>
                        </span>
                        <span class="forum-user-details-date">
                            <?= \mpf\modules\forum\components\Translator::get()->translate("Member since"); ?>
                            <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->getSectionUser($subcategory->category->section_id)->member_since, false, false)); ?>
                        </span>
                    </div>
                </td>
                <td class="forum-reply-form-column">
                    <?php $this->displayComponent("replyform", ['model' => $replyModel]); ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $this->displayComponent("pagelist", [
        'elementsName' => \mpf\modules\forum\components\Translator::get()->translate('replies'),
        'totalElements' => $thread->first_level_replies . ' ( ' . $thread->replies . ' )',
        'visibleElements' => count($replies),
        'totalPages' => (int)(($thread->first_level_replies / \mpf\modules\forum\components\Config::value('FORUM_REPLIES_PER_PAGE')) + (($thread->first_level_replies % \mpf\modules\forum\components\Config::value('FORUM_REPLIES_PER_PAGE')) ? 1 : 0)),
        'currentPage' => $currentPage
    ]); ?>

</div>

<script>
    $(document).ready(function () {
        $('.reply-to-existing-reply').each(function () {
            var className = $(this).attr('href').substring(11);
            $(this).click(function () {
                $('.forum-subreply-' + className).show();
                var _self = $('.forum-subreply-' + className, this.parentNode.parentNode);
                setTimeout(function () {
                    $('textarea', _self).focus();
                }, 200);
            });
        });
    });

    /**
     * Called after a user submits a vote for a reply
     * @param reply
     * @param postData
     * @param element
     */
    function afterReplyVote(reply, postData, element) {
        reply = reply.split(':');
        $("#number-of-points-for-reply-" + postData.level + "-" + postData.id).text(reply[0]);

    }

    function hideReplyForm(element) {
        $(element.parentNode.parentNode.parentNode).hide();
        return false;
    }
</script>