<?php /* @var $this \mpf\modules\forum\controllers\Home */ ?>
<?php /* @var $subcategory \mpf\modules\forum\models\ForumSubcategory */ ?>
<?php /* @var $thread \mpf\modules\forum\models\ForumThread */ ?>
<?php /* @var $replies \mpf\modules\forum\models\ForumReply[] */ ?>
<?php /* @var $replyModel \mpf\modules\forum\models\ForumReply */ ?>
<?php /* @var $currentPage int */ ?>
<?php /* @var $reply \mpf\modules\forum\models\ForumReply */ ?>
<?php /* @var $level int */ ?>
<table class="forum-replies">
    <?php foreach ($reply->replies as $subReply) { ?>
        <tr>
            <td class="forum-user-details">
                <b class="forum-user-details-name">
                    <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $subReply->user_id, 'name' => $subReply->author->name]]), $subReply->author->name); ?>
                </b>
                                <span class="forum-user-details-title">
                                    <?= $subReply->sectionAuthor->title->title; ?>
                                </span>
                <?= \mpf\web\helpers\Html::get()->image($subReply->getAuthorIcon()); ?>
                <span class="forum-user-details-group">
                                    <?= $subReply->sectionAuthor->group->full_name; ?>
                                </span>
                                <span class="forum-user-details-date">
                                    <?= \mpf\modules\forum\components\Translator::get()->translate("Member since"); ?>
                                    <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($subReply->sectionAuthor->member_since, false, false)); ?>
                                </span>
            </td>
            <td class="forum-reply-content">
                <?php if ($subReply->canEdit($subcategory->category_id, $subcategory->category->section_id)) { ?>
                    <div class="forum-reply-management-links">
                        <?= \mpf\web\helpers\Html::get()->link(
                            $this->updateURLWithSection(['thread', 'editReply', ['id' => $subReply->id, 'level' => $level]]),
                            \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/story-editor.png", "Edit reply")
                        ); ?>
                        <?= \mpf\web\helpers\Html::get()->postLink(
                            $this->updateURLWithSection(['thread', 'deleteReply']),
                            \mpf\web\helpers\Html::get()->mpfImage("oxygen/22x22/actions/dialog-cancel.png", "Delete reply"),
                            ['id' => $subReply->id, 'level' => $level], [],
                            false,
                            \mpf\modules\forum\components\Translator::get()->translate("Are you sure you want to delete this reply? You can`t undo this action!")
                        ); ?>
                    </div>
                <?php } ?>
                <div
                    class="forum-reply-content-date"><?= \mpf\helpers\DateTimeHelper::get()->niceDate($subReply->time); ?></div>
                <?= $subReply->getContent(); ?>
                <?php if ($subReply->edited) { ?>
                    <div class="forum-reply-edit-details">
                        <?php if ($subReply->edit_user_id == $subReply->user_id) { ?>
                            <?= \mpf\modules\forum\components\Translator::get()->translate("Edited"); ?>
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($subReply->edit_time, false, false); ?>
                        <?php } else { ?>
                            <?= \mpf\modules\forum\components\Translator::get()->translate("Edited by"); ?>
                            <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => $subReply->edit_user_id]]), $subReply->editor->name); ?>
                            <?= \mpf\helpers\DateTimeHelper::get()->niceDate($subReply->edit_time, false, false); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!$reply->deleted && 6 != $level && \mpf\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
                    <div class="forum-thread-reply-actions">
                            <?= \mpf\web\helpers\Html::get()->link('#reply-for-' . $level . '-' . $reply->id, \mpf\modules\forum\components\Translator::get()->translate('Reply'), ['class' => 'new-reply-button reply-to-existing-reply']); ?>
                    </div>
                <?php } ?>
                <?= \mpf\modules\forum\components\Config::value('FORUM_THREAD_SIGNATURE_SEPARATOR'); ?>
                <?= $subReply->sectionAuthor->getSignature(); ?>
                <?php $this->display("_replies", ['reply' => $subReply, 'level' => $level + 1]); ?>
            </td>
        </tr>
    <?php } ?>
    <?php if (!$reply->deleted && (!$thread->closed || \mpf\modules\forum\components\UserAccess::get()->isCategoryModerator($subcategory->category_id, $this->sectionId)) && \mpf\modules\forum\components\UserAccess::get()->canReplyToThread($subcategory->category_id, $this->sectionId)) { ?>
        <tr class="forum-reply-form forum-subreply forum-subreply-<?= $level-1;?>-<?= $reply->id; ?>" >
            <td class="forum-user-details"><a name="reply-for-<?= ($level -1) .'-' . $reply->id; ?>"></a>
                <b class="forum-user-details-name">
                    <?= \mpf\web\helpers\Html::get()->link($this->updateURLWithSection(['user', 'index', ['id' => \mpf\WebApp::get()->user()->id, 'name' => \mpf\WebApp::get()->user()->name]]), \mpf\WebApp::get()->user()->name); ?>
                </b>
                    <span class="forum-user-details-title">
                        <?= ($t = \mpf\modules\forum\components\UserAccess::get()->getUserTitle($subcategory->category->section_id)) ? $t->title : '-'; ?>
                    </span>
                <?= \mpf\web\helpers\Html::get()->image(\mpf\modules\forum\components\Config::value('USER_ICON_FOLDER_URL') . (\mpf\WebApp::get()->user()->icon ?: 'default.png')); ?>
                <span class="forum-user-details-group">
                    <?= ($g = \mpf\modules\forum\components\UserAccess::get()->getUserGroup($subcategory->category->section_id)) ? $g->full_name : '-'; ?>
                </span>
                <span class="forum-user-details-date">
                    <?= \mpf\modules\forum\components\Translator::get()->translate("Member since"); ?>
                    <?= lcfirst(\mpf\helpers\DateTimeHelper::get()->niceDate($thread->getSectionUser($subcategory->category->section_id)->member_since, false, false)); ?>
                </span>

            </td>
            <td class="forum-reply-form-column">
                <?php $this->displayComponent("replyform", ['model' => $replyModel, 'level' => $level, 'parent' => $reply->id, 'cancel' => true, 'height' => '50px', 'name' => "reply_to_" . $reply->author->name]); ?>
            </td>
        </tr>
    <?php } ?>
</table>
