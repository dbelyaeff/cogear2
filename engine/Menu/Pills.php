<?php
/**
 * Menu Pills
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Menu_Pills extends Menu_Auto{
    /**
     * Constructor
     */
    public function __construct($options) {
        isset($options['template']) OR $options['template'] = 'Twitter_Bootstrap.pills';
        parent::__construct($options);
    }
    
}