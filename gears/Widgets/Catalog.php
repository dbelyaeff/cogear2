<?php
/**
 * Widgets catalog
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Widgets_Catalog extends Options {
    public $widgets = array();
    protected $packages = array();

    /**
     *  Constructor
     *
     * @param type $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->widgets = new Core_ArrayObject($this->widgets);
        $this->packages = new Core_ArrayObject($this->packages);
        event('widgets.register',$this->widgets);
    }
    /**
     * Harvest widgets
     */
    private function harvestWidgets(){
        foreach(cogear()->gears as $gear){
            if($widgets = $gear->widgets()){
                foreach($widgets as $class){
                    if(class_exists($class)){
                        $this->widgets->$class = new Core_ArrayObject();
                        $this->widgets->$class->adopt($class::info());
                        $this->packages->{$this->widgets->$class->package} OR $this->packages->{$this->widgets->$class->package} = new Core_ArrayObject();
                        $this->packages->{$this->widgets->$class->package}->append($this->widgets->$class);
                    }
                }
            }
        }
    }
    /**
     * Render
     */
    public function render(){
        $this->harvestWidgets();
        $tpl = new Template('Widgets/templates/catalog');
        $tpl->packages = $this->packages;
        return $tpl->render();
    }
}