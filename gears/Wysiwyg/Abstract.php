<?php
/**
 * Abstract WYSIWYG editor
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          WYSIWYG
 * @version		$Id$
 */
abstract class Wysiwyg_Abstract extends Form_Element_Textarea {
    protected static $is_loaded;
    public $options;

    /**
     * This is the place where assets must be loaded
     */
    abstract public function load();
    /**
     * Render editor
     */
    public function render(){
        if(!self::$is_loaded){
            $this->load();
            self::$is_loaded = TRUE;
        }
        return parent::render();
    }
    
}