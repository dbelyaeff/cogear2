<?php

/**
 * Шестерёнка Роли
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Roles_Gear extends Gear {

    protected $hooks = array(
        'user.register' => 'hookSetDefaultRole',
    );
    /**
     * Set default role after user if registered
     *
     * @param object $User
     */
    public function hookSetDefaultRole($User){
        $User->update(array('role'=>config('roles.default',100)));
    }
    /**
     * Возврващает массив ролей для поля select
     */
    public function getSelectRoleValues(){

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
        if($user = user($uid)){
            return $user->role;
        }
        return 0;
    }
    else if($user = user()){
        return $user->role;
    }
    return 0;
}