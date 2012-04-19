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
    protected $package = '';
    protected $order = 0;
    
    /**
     * Init
     */
    public function init(){
        parent::init();
        if(Ajax::is() && $this->input->get('modal')){
            $uri = $this->router->getUri();
            $window = new Modal_Window(array(
                'name' => 'ajax',
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
        $login_window = new Modal_Window(array(
            'name' => 'login',
            'header' => t('Login','User'),
            'source' => l('/user/login #form-user-login'),
        ));
        $login_window->show();
    }
}