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
     * Stack name
     * 
     * @var string 
     */
    protected $name;

    /**
     * Constructor
     * 
     * @param   string  $name
     */
    public function __construct($name) {
        $this->name = $name;
        parent::__construct();
    }
    /**
     * Render stack
     * 
     * @param string $glue
     * @return string
     */
    public function render($glue = ' ') {
        event('stack.' . $this->name, $this);
        return $this->toString($glue);
    }

}
