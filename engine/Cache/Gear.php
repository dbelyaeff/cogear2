<?php
/**
 * Cache gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Cache
 * @subpackage
 * @version		$Id$
 */
class Cache_Gear extends Gear {
    protected $name = 'Cache';
    protected $description = 'Perform caching.';
    protected $package = 'Performance';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->attach(new Cache_Object());
    }
}
/**
 * Caching alias
 *
 * @param type $name
 * @param type $value
 * @param type $tags
 * @param type $ttl
 * @return type
 */
function cache($name,$value = '',$tags = array(),$ttl = 3600){
    if($value){
        return cogear()->cache->write($name,$value,$tags,$ttl);
    }
    else {
        return cogear()->cache->read($name);
    }
}