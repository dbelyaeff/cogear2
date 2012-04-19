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
    protected $refresh_flag;
    protected $order = -9;
    protected $hooks = array(
        'exit' => 'save',
    );

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->rules = new Config();
        // There we have to add hook manually, becase Access init load after User init.
        hook('user.refresh',array($this,'reset'));
    }

    /**
     * Initialize
     */
    public function init() {
        parent::init();
        $this->setRules();
        $this->setRights();
    }

    /**
     * Set rigths for current user
     *
     * @param   mixed   $rights
     */
    protected function setRights($rights = array()) {
        if(!$rights && $this->session->get('access') && 0 !== $this->session->access->count()){
            return;
        }
        $this->session->set('access',new Config());
        if ($rights) {
            foreach ($rights as $key => $right) {
                if (is_bool($right)) {
                    $rule = $key;
                } elseif (is_object($right) && $right instanceof Access_Rule) {
                    $rule = $right->name;
                } else {
                    $rule = $right;
                }
                $this->session->access->set($rule, TRUE);
            }
        } else {
            if ($this->user->id) {
                $rule = new Access_Rule();
                $rule->uid = $this->user->id;
                if ($rules = $rule->findAll()) {
                    $this->setRights($rules);
                }
                foreach ($this->user->roles as $role) {
                    if ($rules = $this->getRoleRights($role)) {
                        $this->setRights($rules);
                    }
                }
            } else {
                $rule = new Access_Rule();
                if ($rules = $this->getRoleRights(0)) {
                    $this->setRights($rules);
                }
            }
        }
    }

    /**
     * Check rules
     *
     * @param string $rule
     * @return boolean
     */
    public function check($rule) {
        if (!$this->rules->get($rule)) {
            $this->rules->set($rule, TRUE);
            $this->refresh_flag = TRUE;
        }
        if ($this->user->id == 1) {
            return TRUE;
        }
        if (!$this->session->access) {
            return FALSE;
        }
        return $this->session->access->get($rule) ? TRUE : FALSE;
    }

    /**
     * Get rights for user
     *
     * @param int $uid
     * @return mixed
     */
    public function getUserRights($uid) {
        $uid OR $uid = $this->user->id;
        $rule = new Access_Rule();
        $rule->uid = $uid;
        return $rule->findAll();
    }

    /**
     * Set rights for user
     *
     * @param   int $uid
     * @param   array   $rights
     */
    public function addUserRights($rights, $uid=NULL) {
        $uid OR $uid = $this->user->id;
        !is_array($rights) && $rights = (array) $rights;
        foreach ($rights as $right) {
            $rule = new Access_Rule();
            $rule->uid = $uid;
            $rule->name = $right;
            $rule->save();
        }
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
        foreach ($rights as $right) {
            $rule = new Access_Rule();
            $rule->uid = $uid;
            $rule->name = $right;
            $rule->delete();
        }
    }

    /**
     * Get rights for group
     *
     * @param int $rid
     * @return mixed
     */
    public function getRoleRights($rid) {
        $rule = new Access_Rule();
        $rule->rid = $rid;
        $rule->uid = 0;
        return $rule->findAll();
    }

    /**
     * Set rights for role
     *
     * @param   int $role
     * @param   array   $rights
     * @param   boolean $refresh
     */
    public function addRoleRights($rights, $role) {
        !is_array($rights) && $rights = (array) $rights;
        foreach ($rights as $right) {
            $rule = new Access_Rule();
            $rule->rid = $role;
            $rule->name = $right;
            $rule->save();
        }
    }

    /**
     * Remove rights from role
     *
     * @param   int $role
     * @param   array   $rights
     */
    public function removeRoleRights($rights, $role) {
        !is_array($rights) && $rights = (array) $rights;
        foreach ($rights as $right) {
            $rule = new Access_Rule();
            $rule->rid = $role;
            $rule->name = $right;
            $rule->delete();
        }
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
            $this->system_cache->write('access/rules', $this->rules->toArray(), array('access'));
        }
    }

}

function access($rule) {
    return cogear()->access ? cogear()->access->check($rule) : TRUE;
}

function allow_role($rules, $role = NULL) {
    cogear()->access->addRoleRights($rules, $role);
}

function allow_user($rules, $uid = NULL) {
    cogear()->access->addUserRights($rules, $uid);
}