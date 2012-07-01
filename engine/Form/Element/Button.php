<?php
/**
 *  Form Element Submit
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Button extends Form_Element_Abstract{
    protected $type = 'button';
    /**
     * Buttons data shouldn't be in result
     *
     * @return NULL
     */
    public function result(){
        $method = strtolower($this->form->method);
        $this->value = cogear()->input->$method($this->name);
        $this->is_fetched = TRUE;
        return $this->value ? TRUE : NULL;
    }
    
    public function render(){
        $this->prepareOptions();
        $this->value = $this->label;
        $this->options->label = '';
        $this->code = HTML::input($this->options);
        return parent::render();
    }
}
