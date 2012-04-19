<?php
/**
 * Modal gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Modal_Gear extends Gear {

    protected $name = 'Modal';
    protected $description = 'Modal windows manager';

    /**
     * Init
     */
    public function init(){
        parent::init();
        if(Ajax::is() && $this->input->get('modal')){
            $uri = $this->router->getUri();
            $window = new Modal_Window(array(
                'name' => $this->input->get('name','ajax'),
                'header' => $this->input->get('header'),
                'source' => l('/'.$uri.' #'.$this->input->get('modal')),
                'settings' => array(
                    'show' => TRUE,
                )
            ));
            $ajax = new Ajax();
            // Delete duplicate if exists
            $ajax->append('$("#'.$window->id().'").remove()');
            $ajax->append('$("'.Ajax::escape($window->render()).'").appendTo("#content");');
            $ajax->append($window->script());
            $ajax->send();
        }
    }
    
    /**
     * Hook menu
     * 
     * @param string $name
     * @param array $menu 
     */
    public function menu($name,$menu){
        if($name == 'navbar'){
            if($this->user->id == 0){
                $menu[0]->options->link = '#'.$menu[0]->link.'?'.Ajax::query(array(
                    'modal' => 'form-user-login',
                    'header' => t('Login','User'),
                    'name' => 'login',
                ));
                $menu[1]->options->link = '#'.$menu[1]->link.'?'.Ajax::query(array(
                    'modal' => 'form-user-register',
                    'header' => t('Register','User'),
                    'name' => 'register',
                ));
            }
        }
    }
}