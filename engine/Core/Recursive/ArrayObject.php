<?php
/**
 * Recursive Array Object 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Recursive_ArrayObject extends Core_ArrayObject {
    /**
     * Get Iterator overload
     * 
     * @return RecursiveArrayIterator
     */
    public function getIterator() {
        return new RecursiveArrayIterator($this);
    }
}

