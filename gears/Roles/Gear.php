<?php

/**
 * Roles gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Roles_Gear extends Gear {

    protected $name = 'Roles';
    protected $description = 'User roles';
    protected $order = 0;
    protected $hooks = array(
        'user.verified' => 'hookSetDefaultRole',
    );
    protected $is_core = TRUE;
    /**
     * Set default role after user if registered
     *
     * @param object $User
     */
    public function hookSetDefaultRole($User){
        $User->update(array('role'=>config('roles.default',100)));
    }
}

/**
 * Shortcut for get user role
 *
 * @param int $uid
 * @return int
 */
function role($uid = null){
    if($uid){
        $user = new User();
        $user->id = $uid;
        if($user->find()){
            return $user->role;
        }
        return 0;
    }
    return cogear()->user->role;
}