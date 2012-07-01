<?php

/**
 * Cache
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Cache_Object extends Object {

    /**
     * Initiate cache
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        $defaults = array(
            'adapter' => 'Cache_Adapter_File',
            'path' => CACHE . DS
        );
        $options = array_merge($defaults, $options);
        parent::__construct($options);
        if (class_exists($options['adapter'])) {
            $this->attach(new $options['adapter']($options));
        }
    }

}