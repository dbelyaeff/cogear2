<?php

/**
 *  Redactor WYSIWYG editor 
 * 
 *  http://imperavi.ru/redactor/
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Wysiwyg_Editor extends Wysiwyg_Abstract {

    protected $options = array(
        'focus' => TRUE,
        );
    /**
     * Constructor
     * 
     * @param array $options 
     */
    public function __construct($options  = array()) {
        parent::__construct($options);
    }
    /**
     * Load scripts
     */
    public function load() {
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render() {
        return parent::render();
    }

}
