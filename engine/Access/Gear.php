<?php

/**
 * Access control gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Access
 * @version		$Id$
 */
class Access_Gear extends Gear {

    protected $name = 'Access';
    protected $description = 'Access control gear';
    protected $rules;
    protected $roles;
    protected $refresh_flag;
    protected $order = -9;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->rules = new Core_ArrayObject();
        $this->roles = new Core_ArrayObject();
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        if ($rules = $this->system_cache->read('access/rules', TRUE)) {
            $this->rules->adopt($rules);
        }
        if ($roles = $this->system_cache->read('access/roles', TRUE)) {
            $this->roles->adopt($roles);
        }
        $this->getRights();
        hook('exit', array($this, 'save'));
    }

    /**
     * Reset access data
     * 
     * @param string $action 
     */
    public function index($action = NULL) {
        if ($this->user->id != 1) {
            back();
        }
        switch ($action) {
            case 'reset':
                $this->clear();
                flash_success(t('Access rights have been reseted successfully!', 'Access'));
                back();
                break;
        }
    }

    /**
     * Check rules
     *
     * @param string $rule
     * @return boolean
     */
    public function check($rule) {
        if (!$this->rules->offsetExists($rule)) {
            $this->rules->offsetSet($rule, TRUE);
            $this->refresh_flag = TRUE;
        }
        if ($this->user->id == 1) {
            return TRUE;
        }
        if (!$this->session->access) {
            return FALSE;
        }
        return $this->session->access->{$rule};
    }

    /**
     * Get rules
     * 
     * @return Core_ArrayObject
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Get rights for user and his role
     */
    public function getRights() {
        DEVELOPMENT && $this->reset();
        if ($this->session->access !== NULL) {
            return;
        }
        $access = new Core_ArrayObject();
        if($role = $this->getRoleRights($this->user->role)){
            $access->mix($role);
        }
        if($user = $this->getUserRights($this->user->id)){
            $access->mix($user);
        }
        $this->session->access = $access;
    }

    /**
     * Get rights for user
     * 
     * @param int $uid
     * @return mixed 
     */
    public function getUserRights($uid) {
        return $this->system_cache->read('access/users/' . $uid, TRUE);
    }

    /**
     * Set rights for user
     * 
     * @param   int $uid
     * @param   array   $rights
     */
    public function addUserRights($rights, $uid=NULL, $refresh = TRUE) {
        $uid OR $uid = $this->user->id;
        !is_array($rights) && $rights = (array) $rights;
        $access = $this->getUserRights($uid) OR $access = new Core_ArrayObject();
        foreach ($rights as $right) {
            if (!$access->offsetExists($right)) {
                $access->offsetSet($right, TRUE);
            }
        }
        $refresh && $this->system_cache->write('access/users/' . $uid, $access);
    }

    /**
     * Remove rights from user
     * 
     * @param   int $uid
     * @param   array   $rights
     */
    public function removeUserRights($rights, $uid=NULL) {
        $uid OR $uid = $this->user->id;
        !is_array($rights) && $rights = (array) $rights;
        $access = $this->getUserRights($uid) OR $access = new Core_ArrayObject();
        foreach ($rights as $right) {
            if (!$access->offsetExists($right)) {
                $access->offsetUnset($right);
            }
        }
        $this->system_cache->write('access/users/' . $uid, $access);
    }

    /**
     * Get rights for group
     * 
     * @param int $role
     * @return mixed 
     */
    public function getRoleRights($role) {
        return $this->roles->$role;
    }

    /**
     * Set rights for role
     * 
     * @param   int $role
     * @param   array   $rights
     * @param   boolean $refresh
     */
    public function addRoleRights($rights, $role=NULL, $refresh = TRUE) {
        $role OR $role = $this->user->role;
        !is_array($rights) && $rights = (array) $rights;
        if (!$this->roles->$role) {
            $this->roles->$role = new Core_ArrayObject();
        }
        foreach ($rights as $right) {
            $this->roles->$role->offsetExists($right) OR $this->roles->$role->offsetSet($right, TRUE);
        }
        $this->refresh_flag = $refresh;
    }

    /**
     * Remove rights from role
     * 
     * @param   int $role
     * @param   array   $rights
     */
    public function removeRoleRights($rights, $role=NULL) {
        $role OR $role = $this->user->role;
        !is_array($rights) && $rights = (array) $rights;
        foreach ($rights as $right) {
            $this->roles->$role && $this->roles->$role->offsetUnset($right);
        }
        $this->refresh_flag = TRUE;
    }

    /**
     * Public function clear
     */
    public function reset() {
        $this->session->remove('access');
    }

    /**
     * Clear all stored cache data
     */
    public function clear() {
        $this->system_cache->removeTags('access');
    }

    /**
     * Save rules
     */
    public function save() {
        if ($this->refresh_flag) {
            $this->system_cache->write('access/rules', $this->rules, array('access'));
            $this->system_cache->write('access/roles', $this->roles, array('access'));
        }
    }

    /**
     * 
     */
    public function _403() {
        $this->response->header('Status', '403 ' . Response::$codes[403]);
        event('exit');  
        exit(t('You don\'t have enought permissions to access this page.'));
    }

}

function access($rule) {
    return cogear()->access ? cogear()->access->check($rule) : TRUE;
}

function page_access($rule) {
    $cogear = getInstance();
    if (access($rule)) {
        return TRUE;
    } else {
        return _403();
    }
}

function _403() {
    $cogear = getInstance();
    $cogear->router->exec(array($cogear->access, '_403'));
}

function allow_role($rules, $role = NULL) {
    cogear()->access->addRoleRights($rules, $role);
}

function allow_user($rules, $uid = NULL) {
    cogear()->access->addUserRights($rules, $uid);
}