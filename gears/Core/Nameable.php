<?php

/**
 * Any object that can has name
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

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