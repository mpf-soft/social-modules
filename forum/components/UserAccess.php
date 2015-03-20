<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 16.03.2015
 * Time: 10:56
 */

namespace app\modules\forum\components;


use app\modules\forum\models\ForumSection;
use mpf\base\Object;
use mpf\WebApp;

class UserAccess extends Object{
    /**
     * @var UserAccess
     */
    private static $self;

    /**
     * @return UserAccess
     */
    public static function get(){
        if (!self::$self)
            self::$self = new self();
        return self::$self;
    }

    /**
     * @var \app\modules\forum\models\ForumSection[]
     */
    private $sections = [];

    /**
     * Check if is admin for this section of the forum. If is admin then it can create/delete categories,
     * create/delete groups and set another admins.
     * @param int $sectionId
     * @return bool
     */
    public function isSectionAdmin($sectionId){
        if (!isset($this->sections[$sectionId])){
            $this->sections[$sectionId] = ForumSection::findByPk($sectionId);
        }
        if (!$this->sections[$sectionId]){ //section doesn't exists
            return false;
        }
        if ($this->sections[$sectionId]->owner_user_id == WebApp::get()->user()->id){ // section creator so it is always true
            return true;
        }
        return true;
    }

    /**
     * Checks if is moderator of this section. If so it will be moderator for all categories also and it can delete, edit or
     * move threads, promote users or mute users and so on.
     * @param int $sectionId
     * @return bool
     */
    public function isSectionModerator($sectionId){
        return true;
    }

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function isCategoryAdmin($categoryId, $sectionId){
        if ($this->isSectionAdmin($sectionId)){
            return true;
        }
        return true;
    }

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function isCategoryModerator($categoryId, $sectionId){
        if ($this->isSectionModerator($sectionId)){
            return true;
        }
        return true;
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function canCreateNewThread($categoryId){
        return true;
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function canReplyToThread($categoryId){
        return true;
    }

    /**
     * @param int $sectionId
     * @param int $categoryId
     * @return bool
     */
    public function canRead($sectionId, $categoryId = null){
        return true;
    }
}