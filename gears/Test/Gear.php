<?php

/**
 *  gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Test_Gear extends Gear {

    protected $name = '';
    protected $description = '';
    protected $package = '';
    protected $order = 0;

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($action = '', $subaction = NULL) {
        $window = new Modal_Window(array(
                    'header' => 'Test',
                    'body' => 'Test',
                    'name' => 'test',
                    'actions' => array(
                        'go' => array(
                            'link' => '/',
                            'label' => 'Go',
                            'class' => 'btn btn-primary',
                        ),
                        'close' => array(
                            'label' => 'Close',
                            'class' => 'btn btn-warning modal-close',
                        ),
                    ),
                    'settings' => array(
                        'show' => TRUE
                    ),
                ));
        $window->show();
    }

    /**
     * Custom dispatcher
     * 
     * @param   string  $subaction
     */
    public function action_index($subaction = NULL) {
        
    }

}