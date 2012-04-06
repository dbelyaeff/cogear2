<?php

/**
 * Request gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Request_Gear extends Gear {

    protected $name = 'Request';
    protected $description = 'Manage browser input';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->adapter = new Request_Object();
        cogear()->request = $this;
    }
}