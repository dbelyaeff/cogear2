<?php
/**
 * Twitter boostrap navbar
 * 
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Twitter_Bootstrap
 * @version		$Id$
 */
class Twitter_Bootstrap_Navbar extends Menu_Auto {
    /**
     * Constructor
     * 
     * @param array $options 
     */
    public function __construct($options) {
        $options['template'] = 'Twitter_Bootstrap.navbar';
        $options['class'] = 'navbar-fixed-top';
        parent::__construct($options);
    } 
}