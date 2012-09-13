<?php

/**
 * Any object that can has name
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Nameable extends Options {

    protected $name;
    /**
     * Helps to transform form names to jQuery-readable
     *
     * @const
     */
    const SEPARATOR = '-';
    /**
     * Get element id
     * 
     * @return string
     */
    public function getId() {
        return preg_replace('#([^0-9a-z-])ismU#', self::SEPARATOR, $this->name);
    }

}