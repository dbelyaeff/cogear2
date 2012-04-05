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
        $this->value = isset($this->form->request[$this->name]) ? $this->form->request[$this->name] : FALSE;
        $this->is_fetched = TRUE;
        return isset($this->form->request[$this->name]) ? TRUE : NULL;
    }
    
    public function render(){
        $this->getAttributes();
        $this->attributes->value = $this->label;
        $this->attributes->label = '';
        $this->code = HTML::input($this->attributes);
        return parent::render();
    }
}
