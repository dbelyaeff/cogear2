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
    /**
     * Constructor
     *
     * @param type $options
     */
    public function __construct($options) {
        $options['template'] = 'Form.checkbox';
        parent::__construct($options);
    }
    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $method = $this->form->method;
        $this->value = cogear()->input->$method($this->name) ? 1 : 0;
        $this->is_fetched = TRUE;
        return $this->validate() ? $this->value : FALSE;
    }
    /**
     * Prepare options
     * @return type
     */
    public function prepareOptions() {
        $this->options->checked = $this->value ? 'checked' : '';
        parent::prepareOptions();
        return $this->options;
    }
}
