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
        'user.store' => 'userAttachRoles',
        'user.insert' => 'userSetDefaultRole',
        'user.delete' => 'userDeleteClean',
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Menu
     * 
     * @param string $name
     * @param object $menu 
     */
    public function menu($name, &$menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
                    'link' => l('/admin/roles'),
                    'label' => icon('star-empty') . ' ' . t('Roles', 'Roles.admin'),
                    'order' => 120,
                ));
                break;
        }
    }

    /**
     * Attach roles to user
     * 
     * @param type $User 
     */
    public function userAttachRoles($User) {
        $User->roles = $this->getRoles($User->id);
    }
    
    /**
     * Set default role after user if registered
     *
     * @param object $User 
     */
    public function userSetDefaultRole($User){
        $role = new Roles_User();
        $role->id = $User->id;
        $role->rid = config('role.default',100);
        $role->save();
    }
    /**
     * Delete roles after user is deleted
     *
     * @param object $User 
     */
    public function userDeleteClean($User){
        $role = new Roles_User();
        $role->id = $User->id;
        $role->delete();
    }

    /**
     * Get roles by user id
     * 
     * @param int $uid 
     * @return  array
     */
    public function getRoles($uid) {
        $roles = new Roles_User();
        $roles->uid = $uid;
        $data = new Core_ArrayObject();
        if ($result = $roles->findAll()) {
            foreach ($result as $role) {
                $data->offsetSet($role->rid, $role->rid);
            }
            return $data;
        }
        return $data;
    }

}