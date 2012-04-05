<?php
/**
 *  Form Element Checkbox
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Checkbox extends Form_Element_Abstract{
    protected $type = 'checkbox';
    protected $attributes = array('class'=>'inline left');
    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $this->value = isset($this->form->request[$this->name]) ? 1 : 0;
        $this->is_fetched = TRUE;
//        $this->filter();
        return $this->validate() ? $this->value : FALSE;
    }
    public function getAttributes() {
        $this->value && $this->attributes->checked = 'checked';
        parent::getAttributes();
        $this->attributes->offsetUnset('value');
        return $this->attributes;
    }
}
