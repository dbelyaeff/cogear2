<?php

/**
 * Friends gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Friends_Gear extends Gear {

    protected $name = 'Friends';
    protected $description = 'Manage friends';
    protected $hooks = array(
        'user.navbar' => 'hookUserNavbar',
    );

    public function hookUserNavbar($Navbar) {
        if ($Navbar->object->id != $this->user->id) {
            $is_friend = $this->check($Navbar->object->id);
            $link = l('/friends/status/'.$Navbar->object->id.'/');
            $link = sprintf('<a href="%s" class="ajax btn btn-mini %s">%s</a>',$link,$is_friend ? 'btn-success' : 'btn-danger',t($is_friend ? 'Friend':'Unfriend','Friends'));
            $Navbar->append($link);
        }
    }
    
    /**
     * Check user to be a friend
     * 
     * @reutrn boolean
     */
    public function check($uid){
        return TRUE;
    }
    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index_action($action = '', $subaction = NULL) {
        
    }
    
    /**
     * Change status
     * 
     * @param type $id
status     */
    public function status_action($id = 0){
        $user = new User();
        $user->id = $id;
        new Friends();
        if($id && $user->find()){
            $status = $this->check($id);
            $form = new Form('Friends.status');
            $form->init();
            switch($status){
                case FALSE:
                    $form->title->options->label = t('Remove %s from friends?','Friends',$user->getListView());
                    break;
                default:
                case TRUE:
                $form->title->options->label = t('Add %s to friends?','Friends',$user->getListView());
            }
            if($result = $form->result()){
                if($result->yes){
                    $friends = new Friends_Object();
                }
                else {
                    redirect($user->getLink());
                }
            }
            $form->show();
        }
        else {
            return event('404');
        }
    }
}