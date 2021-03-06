<?php
/**
 * Created by MPF Framework.
 * Date: 2015-04-02
 * Time: 14:33
 */

namespace mpf\modules\forum\models;

use mpf\datasources\sql\DbRelation;
use mpf\datasources\sql\DbRelations;
use mpf\WebApp;

/**
 * Class ForumReplySixth
 * @package app\models
 * @property int $reply_id
 * @property \mpf\modules\forum\models\ForumReplyFifth $parent
 * @property \mpf\modules\forum\models\ForumReplySeventh[] $replies
 */
class ForumReplySixth extends ForumReply {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_replies_sixth";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return parent::getLabels() + ['reply_id' => 'Parent'];
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        $old =  parent::getRelations();
        unset($old['replies']);
        $old['parent'] = [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumReplyFifth', 'reply_id'];
        $old['replies'] = [DbRelations::HAS_MANY, '\mpf\modules\forum\models\ForumReplySeventh', 'reply_id'];
        $old['myVote'] = DbRelation::hasOne(ForumReplyVote::className())->columnsEqual('id', 'reply_id')->hasValue('level', 6)->hasValue('user_id', WebApp::get()->user()->isConnected()?WebApp::get()->user()->id:0);
        return $old;
    }
}
