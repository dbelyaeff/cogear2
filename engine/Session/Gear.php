<?php

/**
 * Sessions gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Session_Gear extends Gear {

    protected $name = 'Sessions';
    protected $description = 'Handle sessions.';
    protected $order = -1000;

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->adapter = new Session_Object(array('name' => 'session'));
    }

}