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
class Admin_Menu extends Menu_Auto{
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('admin', 'Admin/templates/menu',Url::gear('admin'));
        //hook('before',array($this,'output'));
        // If you want do mix this menu with user menu — just uncomment the line
        hook('menu.user',array($this,'mixWith'),NULL,'user','admin');
    }

}