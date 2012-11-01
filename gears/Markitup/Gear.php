<?php

/**
 * Markitup gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Markitup_Gear extends Gear {

    protected $name = 'Markitup';
    protected $description = 'Markitup editor';
    protected $package = 'Wysiwyg';
    protected $order = 10;
    protected $required = array('Wysiwyg');
   
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        Wysiwyg_Gear::$editors[ 'markitup'] = 'Markitup_Editor';
    }


    /**
     * Skip assets loading
     */
    public function loadAssets() {
//        parent::loadAssets();
    }

}