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
    protected $order = -1000;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->adapter = new Assets_Harvester();
        cogear()->assets = $this;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        $cogear = new Core_ArrayObject();
        $cogear->settings = new Core_ArrayObject();
        $cogear->settings->site = config('site.url');
        event('Assets.js.global', $cogear);
        inline_js("
            var cogear = cogear || " . json_encode($cogear) . ";               
", 'head');
    }

}