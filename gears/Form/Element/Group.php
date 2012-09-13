<?php
/**
 *  Form Element Group
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Group extends Form_Element_Abstract{
    /**
     * Constructor
     * 
     * @param type $options 
     */
    public function __construct($options) {
        $options['template'] = 'Form.group';
        parent::__construct($options);
        foreach($this->options->elements as $key=>$element){
            $element->render = FALSE;
            $this->options->form->addElement($key,$element);
        }
    }
    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
    }
    /**
     * Prepare options
     * @return type 
     */
    public function render() {
        $elements = array();
        foreach($this->form->elements as $name=>$element){
            if($this->options->elements->$name){
                $elements[] = $element;
            }
        }
        $tpl = new Template($this->options->template);
        $tpl->assign($this->options);
        $tpl->element = $this;
        $tpl->elements = $elements;
        return $tpl->render();
    }
}
