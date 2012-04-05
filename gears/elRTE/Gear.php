<?php

/**
 * elRTE Gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		elRTE
 * @subpackage          Wysiwyg
 * @version		$Id$
 */
class elRTE_Gear extends Gear {

    protected $name = 'elRTE';
    protected $description = 'Perfect Wysiwyg Editor';
    protected $type = Gear::MODULE;
    protected $order = -11;

    /**
     * Init
     */
    public function init() {
        parent::init();
        Wysiwyg_Gear::$editors['elRTE'] = 'elRTE_Editor';
    }

}
