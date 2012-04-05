<?php
/**
 * Admin menu
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Menu_Tabs extends Menu_Auto{
    /**
     * Constructor
     */
    public function __construct($name,$base = NULL) {
        parent::__construct('tabs_'.$name, 'Menu.tabs',$base);
        hook('content',array($this,'output'));
    }
    
}