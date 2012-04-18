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
        $login_window = new Modal_Window(array(
            'name' => 'login',
            'header' => t('Login','User'),
            'source' => l('/user/login #form-user-login'),
        ));
        $login_window->show();
    }
}