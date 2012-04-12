<?php

/**
 * Router gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Router_Gear extends Gear {

    protected $name = 'Router';
    protected $description = 'Manage routes';
    protected $order = -1000;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->adapter = new Router_Object();
    }
}