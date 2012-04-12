<?php

/**
 * Menu 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Menu_Auto extends Menu_Object {

    /**
     * Constructor
     * 
     * @param   array   options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->init();
    }

    /**
     * Init
     */
    public function init() {
        $cogear = getInstance();
        foreach ($cogear->gears as $gear) {
            if (method_exists($gear, 'menu')) {
                call_user_func_array(array($gear, 'menu'), array($this->options->name, &$this));
            }
        } 
    }

}

