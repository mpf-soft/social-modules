<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 16.03.2015
 * Time: 10:56
 */

namespace app\modules\forum\components;


use app\modules\forum\models\ForumSection;
use app\modules\forum\models\ForumTitle;
use app\modules\forum\models\ForumUser2Section;
use app\modules\forum\models\ForumUserGroup;
use mpf\base\LogAwareObject;
use mpf\web\Session;
use mpf\WebApp;

class UserAccess extends LogAwareObject {
    /**
     * @var UserAccess
     */
    private static $self;

    public $sessionKey = "Forum-UserAccess";

    public $user2Sections = [];

    /**
     * A function that returns true if it's admin or false if not
     * @var callable
     */
    public $isSiteAdminCallback;

    /**
     * A function that returns true if it's moderator or false if not
     * @var callable
     */
    public $isSiteModeratorCallback;

    /**
     * @return UserAccess
     */
    public static function get() {
        if (!self::$self)
            self::$self = new self();
        return self::$self;
    }

    /**
     * @param int $sectionId
     * @param bool $idOnly
     * @return ForumUserGroup|int
     */
    public function getUserGroup($sectionId, $idOnly = false) {
        if (WebApp::get()->user()->isConnected()) {
            if (isset($this->user2Sections[$sectionId]))
                return $idOnly ? $this->user2Sections[$sectionId]['group_id'] : ForumUserGroup::findByPk($this->user2Sections[$sectionId]['group_id']);
        }
        if (!isset($this->sections[$sectionId]))
            $this->sections[$sectionId] = ForumSection::findByPk($sectionId);

        if (!isset($this->sections[$sectionId]))
            return false;

        return $idOnly ? $this->sections[$sectionId]->default_visitors_group_id : ForumUserGroup::findByPk($this->sections[$sectionId]->default_visitors_group_id);
    }

    /**
     * @param int $sectionId
     * @param bool $stringTitleOnly
     * @param bool $idOnly
     * @return ForumTitle|string
     */
    public function getUserTitle($sectionId, $stringTitleOnly = false, $idOnly = false) {
        if (WebApp::get()->user()->isGuest()) {
            return $idOnly ? 0 : ($stringTitleOnly ? '' : null);
        }
        if (!isset($this->user2Sections[$sectionId])) {
            return $idOnly ? 0 : ($stringTitleOnly ? '' : null);
        }
        if ($stringTitleOnly) {
            return $this->user2Sections[$sectionId]['title_string'];
        } elseif ($idOnly) {
            return $this->user2Sections[$sectionId]['title_id'];
        }
        return ForumTitle::findByPk($this->user2Sections[$sectionId]['title_id']);

    }

    public function reloadRights() {
        if (WebApp::get()->user()->isGuest()) {
            return false; // no rights to load if no user is logged in
        }
        Session::get()->delete($this->sessionKey);
        $user2Sections = ForumUser2Section::findAllByAttributes(['user_id' => WebApp::get()->user()->id]);
        $this->user2Sections = [];
        foreach ($user2Sections as $user) {
            $this->user2Sections[$user->section_id] = [
                'muted' => $user->muted,
                'banned' => $user->banned,
                'title_id' => $user->title_id,
                'title_string' => $user->title->title,
                'group_id' => $user->group_id,
                'group_string' => $user->group->full_name,
                'member_since' => $user->member_since,
                'groupRights' => [
                    'admin' => $user->group->admin,
                    'moderator' => $user->group->moderator,
                    'newthread' => $user->group->newthread,
                    'threadreply' => $user->group->threadreply,
                    'canread' => $user->group->canread
                ]
            ];
        }
        Session::get()->set($this->sessionKey, [
            'user2Sections' => $this->user2Sections
        ]);
    }

    public function init($config = []) {
        if (Session::get()->exists($this->sessionKey)) {
            $session = Session::get()->value($this->sessionKey);
            $this->user2Sections = $session['user2Sections'];
        } else {
            $this->reloadRights();
        }

        parent::init($config);
    }

    /**
     * @var \app\modules\forum\models\ForumSection[]
     */
    private $sections = [];

    /**
     * @return bool
     */
    public function isSiteAdmin() {
        if (!$this->isSiteAdminCallback) {
            $this->error("No callback found!");
            return false;
        }
        $callback =$this->isSiteAdminCallback;
        return $callback();
    }

    /**
     * @return bool
     */
    public function isSiteModerator() {
        if (!$this->isSiteModeratorCallback) {
            $this->error("No callback found!");
            return false;
        }
        $callback =$this->isSiteModeratorCallback;
        return $callback();
    }

    /**
     * Check if is admin for this section of the forum. If is admin then it can create/delete categories,
     * create/delete groups and set another admins.
     * @param int $sectionId
     * @return bool
     */
    public function isSectionAdmin($sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSiteAdmin()) {
            return true;
        }
        if (!isset($this->user2Sections[$sectionId])) {
            if (!isset($this->sections[$sectionId])) {
                $this->sections[$sectionId] = ForumSection::findByPk($sectionId);
            }
            if (!$this->sections[$sectionId]) { //section doesn't exists
                return false;
            }
            if ($this->sections[$sectionId]->owner_user_id == WebApp::get()->user()->id) { // section creator so it is always true
                return true;
            }
            return false;
        }

        return (bool)$this->user2Sections[$sectionId]['groupRights']['admin'];
    }

    /**
     * Checks if is moderator of this section. If so it will be moderator for all categories also and it can delete, edit or
     * move threads, promote users or mute users and so on.
     * @param int $sectionId
     * @return bool
     */
    public function isSectionModerator($sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSiteModerator()) {
            return true;
        }
        if (!isset($this->user2Sections[$sectionId])) {
            return false;
        }

        return (bool)$this->user2Sections[$sectionId]['groupRights']['moderator'];
    }

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function isCategoryAdmin($categoryId, $sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSiteAdmin()) {
            return true;
        }
        if ($this->isSectionAdmin($sectionId)) {
            return true;
        }
        if (!isset($this->user2Sections[$sectionId])) {
            return false;
        }
        $categoryRights = ForumUserGroup::getDb()->table('forum_groups2categories')->where("group_id = :id AND category_id = :cat")->setParams([
            ':id' => $this->user2Sections[$sectionId]['group_id'],
            ':cat' => $categoryId
        ])->first();
        if (!$categoryRights)
            return false;
        return (bool)$categoryRights['admin'];
    }

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function isCategoryModerator($categoryId, $sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSiteModerator()) {
            return true;
        }
        if ($this->isSectionModerator($sectionId)) {
            return true;
        }
        if (!isset($this->user2Sections[$sectionId])) {
            return false;
        }
        $categoryRights = ForumUserGroup::getDb()->table('forum_groups2categories')->where("group_id = :id AND category_id = :cat")->setParams([
            ':id' => $this->user2Sections[$sectionId]['group_id'],
            ':cat' => $categoryId
        ])->first();
        if (!$categoryRights)
            return false;
        return (bool)$categoryRights['moderator'];
    }

    /**
     * Different that moderator&admin here it is a possibility that visitors can also create new threads.
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function canCreateNewThread($categoryId, $sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSiteAdmin() || $this->isSiteModerator()){
            return true;
        }
        if (isset($this->user2Sections[$sectionId])) {
            $groupId = $this->user2Sections[$sectionId]['group_id'];
        } else {
            if (!isset($this->sections[$sectionId])) {
                $this->sections[$sectionId] = ForumSection::findByPk($sectionId);
            }
            if (!isset($this->sections[$sectionId])) {
                return false; // section doesn't exists
            }
            $groupId = $this->sections[$sectionId]->default_visitors_group_id;
        }
        $categoryRights = ForumUserGroup::getDb()->table('forum_groups2categories')->where("group_id = :id AND category_id = :cat")->setParams([
            ':id' => $groupId,
            ':cat' => $categoryId
        ])->first();
        if (!$categoryRights) {
            return (bool)ForumUserGroup::findByPk($groupId)->newthread;
        }
        return (bool)$categoryRights['newthread'];
    }

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function canReplyToThread($categoryId, $sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSiteAdmin() || $this->isSiteModerator()){
            return true;
        }

        if (isset($this->user2Sections[$sectionId])) {
            $groupId = $this->user2Sections[$sectionId]['group_id'];
        } else {
            if (!isset($this->sections[$sectionId])) {
                $this->sections[$sectionId] = ForumSection::findByPk($sectionId);
            }
            if (!isset($this->sections[$sectionId])) {
                return false; // section doesn't exists
            }
            $groupId = $this->sections[$sectionId]->default_visitors_group_id;
        }
        $categoryRights = ForumUserGroup::getDb()->table('forum_groups2categories')->where("group_id = :id AND category_id = :cat")->setParams([
            ':id' => $groupId,
            ':cat' => $categoryId
        ])->first();
        if (!$categoryRights) {
            return (bool)ForumUserGroup::findByPk($groupId)->threadreply;
        }
        return (bool)$categoryRights['threadreply'];
    }

    /**
     * @param int $sectionId
     * @param int $categoryId
     * @return bool
     */
    public function canRead($sectionId, $categoryId = null) {
        if ($this->isSiteAdmin() || $this->isSiteModerator()){
            return true;
        }

        if (isset($this->user2Sections[$sectionId])) {
            $groupId = $this->user2Sections[$sectionId]['group_id'];
            if (is_null($categoryId)) {
                return (bool)$this->user2Sections[$sectionId]['groupRights']['canread'];
            }
        } else {
            if (!isset($this->sections[$sectionId])) {
                $this->sections[$sectionId] = ForumSection::findByPk($sectionId);
            }
            if (!isset($this->sections[$sectionId])) {
                return false; // section doesn't exists
            }
            $groupId = $this->sections[$sectionId]->default_visitors_group_id;
        }
        if (is_null($categoryId)) {
            return (bool)ForumUserGroup::findByPk($groupId)->canread;
        }
        $categoryRights = ForumUserGroup::getDb()->table('forum_groups2categories')->where("group_id = :id AND category_id = :cat")->setParams([
            ':id' => $groupId,
            ':cat' => $categoryId
        ])->first();
        if (!$categoryRights) {
            return (bool)ForumUserGroup::findByPk($groupId)->canread;
        }
        return (bool)$categoryRights['canread'];
    }
}