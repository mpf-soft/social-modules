<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 26.04.2015
 * Time: 8:43
 */

namespace mpf\modules\forum\controllers;


use mpf\modules\forum\components\Config;
use mpf\modules\forum\components\Controller;

class Reply extends Controller {

    public function actionIndex($id, $level, $currentPage = 1) {
        $models = ['', 'ForumReply', 'ForumReplySecond', 'ForumReplyThird', 'ForumReplyForth', 'ForumReplyFifth', 'ForumReplySixth', 'ForumReplySeventh', 'ForumReplyEighth',
            'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth', 'ForumReplyNth'];
        $model = "\\mpf\\modules\\forum\\models\\" . $models[$level]; /* @var $model \mpf\modules\forum\models\ForumReply */
        $with = [];
        for ($i = 0; $i <= Config::value('FORUM_MAX_REPLY_LEVELS') - $level; $i++){
            foreach (['author', 'editor', 'authorGroup', 'sectionAuthor', 'sectionAuthor.title', 'replies', 'myVote'] as $child) {
                $with[] = str_repeat('replies.', $i) . $child;
            }
        }
        $entry = $model::findByPk($id, ['with' => $with]);
        /* @var $entry \mpf\modules\forum\models\ForumReply */
        $this->assign("reply", $entry);
        $this->assign("thread", $entry->thread);
        $this->assign("subcategory", $entry->thread->subcategory);
        $this->assign("currentPage", $currentPage);
    }
} 