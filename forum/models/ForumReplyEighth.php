<?php
/**
 * Created by MPF Framework.
 * Date: 2015-04-02
 * Time: 14:33
 */

namespace mpf\modules\forum\models;

use mpf\datasources\sql\DbRelations;

/**
 * Class ForumReplyEighth
 * @package app\models
 * @property int $reply_id
 * @property \mpf\modules\forum\models\ForumReplySeventh
 * @property \mpf\modules\forum\models\ForumReplyNth[] $replies
 */
class ForumReplyEighth extends ForumReply {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "forum_replies_eighth";
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
        $old['parent'] = [DbRelations::BELONGS_TO, '\mpf\modules\forum\models\ForumReplySeventh', 'reply_id'];
        $old['replies'] = [DbRelations::HAS_MANY, '\mpf\modules\forum\models\ForumReplyNth', 'reply_id'];
        return $old;
    }
}
