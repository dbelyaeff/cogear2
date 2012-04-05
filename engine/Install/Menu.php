<?php
/**
 * Install menu
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Install
 * @version		$Id$
 */
class Install_Menu extends Menu_Auto {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('install', 'Install.menu');
        hook('sidebar',array($this,'output'));
    }
}