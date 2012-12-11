<?php

/**
 *  Redactor WYSIWYG editor 
 * 
 *  http://imperavi.ru/redactor/
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Wysiwyg_Editor extends Wysiwyg_Abstract {

    public $options = array(
        'focus' => TRUE,
        );
    /**
     * Конструктор
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
