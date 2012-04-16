<?php

/**
 * Stack — enhanced Core_ArrayObject 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Stack extends Object {
    /**
     * Init
     */
    public function init() {
        event($this->name, $this);
    }

    /**
     * Render stack
     * 
     * @param string $glue
     * @return string
     */
    public function render($glue = ' ') {
        $this->init();
        return $this->toString($glue);
    }

}
