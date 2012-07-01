<?php

/**
 *  Assets gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Assets_Gear extends Gear {

    protected $name = 'Assets';
    protected $description = 'Manage assets';
    protected $order = -999;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->attach(new Assets_Harvester());
        cogear()->assets = $this;
    }

}