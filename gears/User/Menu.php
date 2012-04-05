<?php
/**
 * 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_Menu extends Menu_Auto {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('user', 'User.menu');
        hook('sidebar',array($this,'output'));
    }
}