<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 16.03.2015
 * Time: 10:56
 */

namespace app\modules\forum\components;


use app\modules\forum\models\ForumSection;
use app\modules\forum\models\ForumUserGroup;
use mpf\base\Object;
use mpf\helpers\ArrayHelper;
use mpf\web\Session;
use mpf\WebApp;

class UserAccess extends Object {
    /**
     * @var UserAccess
     */
    private static $self;

    public $sessionKey = "Forum-UserAccess";

    public $userGroups = [];

    public $userSectionsRights = [];

    /**
     * @return UserAccess
     */
    public static function get() {
        if (!self::$self)
            self::$self = new self();
        return self::$self;
    }

    public function getUserGroup($sectionId) {
        if (WebApp::get()->user()->isConnected()) {
            if (isset($this->userGroups[$sectionId]))
                return $this->userGroups[$sectionId];
        }
        if (!isset($this->sections[$sectionId]))
            $this->sections[$sectionId] = ForumSection::findByPk($sectionId);

        return $this->sections[$sectionId]->default_user_group_id;
    }

    public function reloadRights() {
        if (WebApp::get()->user()->isGuest()) {
            return false; // no rights to load if no user is logged in
        }
        Session::get()->delete($this->sessionKey);
        $groupIDs = ArrayHelper::get()->transform(ForumUserGroup::getDb()->table('forums_users2groups')->where("user_id = :user")->setParam(":user", WebApp::get()->user()->id)->get(), 'group_id');
        $this->userGroups = [];
        $groups = ForumUserGroup::findAllByPk($groupIDs);
        $this->userSectionsRights = [];
        foreach ($groups as $group) {
            $this->userGroups[$group->section_id] = $group->id;
            $this->userSectionsRights[$group->section_id] = [
                'admin' => $group->admin,
                'moderator' => $group->moderator,
                'newthread' => $group->newthread,
                'threadreply' => $group->threadreply,
                'canread' => $group->canread
            ];
        }
        Session::get()->set($this->sessionKey, [
            'userGroups' => $this->userGroups,
            'userSectionRights' => $this->userSectionsRights
        ]);
    }

    public function init($config = []) {
        if (Session::get()->exists($this->sessionKey)) {
            $session = Session::get()->value($this->sessionKey);
            $this->userGroups = $session['userGroups'];
            $this->userSectionsRights = $session['userSectionRights'];
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
     * Check if is admin for this section of the forum. If is admin then it can create/delete categories,
     * create/delete groups and set another admins.
     * @param int $sectionId
     * @return bool
     */
    public function isSectionAdmin($sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if (isset($this->userSectionsRights[$sectionId])) {
            return (bool)$this->userSectionsRights[$sectionId]["admin"];
        }
        if (!isset($this->sections[$sectionId])) {
            $this->sections[$sectionId] = ForumSection::findByPk($sectionId);
        }
        if (!$this->sections[$sectionId]) { //section doesn't exists
            return false;
        }
        if ($this->sections[$sectionId]->owner_user_id == WebApp::get()->user()->id) { // section creator so it is always true
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
    public function isSectionModerator($sectionId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        return true;
    }

    /**
     * @param int $categoryId
     * @param int $sectionId
     * @return bool
     */
    public function isCategoryAdmin($categoryId, $sectionId = null) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        if ($this->isSectionAdmin($sectionId)) {
            return true;
        }
        return true;
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
        if ($this->isSectionModerator($sectionId)) {
            return true;
        }
        return true;
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function canCreateNewThread($categoryId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        return true;
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function canReplyToThread($categoryId) {
        if (WebApp::get()->user()->isGuest()) { // no extra checks needed if it's not logged in
            return false;
        }
        return true;
    }

    /**
     * @param int $sectionId
     * @param int $categoryId
     * @return bool
     */
    public function canRead($sectionId, $categoryId = null) {
        return true;
    }
}