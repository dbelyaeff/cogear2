<?php
/**
 * Layout widget
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Pages
 * @version		$Id$
 */
abstract class Layout_Widget{
    protected $data;
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($data = NULL){
        $data && $this->data = $data;
    }
    /**
     * Factory
     *
     * @param string $name
     * @param array $options
     */
    public static function factory($name,$data = NULL){
        $cogear = getInstance();
        if(class_exists($name)){
            $widget = new $name($data);
            if($widget instanceof self){
                return $widget;
            }
            else {
                unset($widget);
            }
        }
    }
    /**
     * Render widget
     *
     * @return string
     */
    abstract function render();

    /**
     * Provide a form to edit widget options
     */
    abstract function options();
}
