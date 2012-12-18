<?php
/**
 *  Form Element Checkbox
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Checkbox extends Form_Element_Abstract{
    /**
     * Конструктор
     *
     * @param type $options
     */
    public function __construct($options) {
        $options['template'] = 'Form/templates/checkbox';
        parent::__construct($options);
    }
    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $method = $this->form->method;
        $this->options->value = cogear()->input->$method($this->options->name) ? 1 : 0;
        $this->is_fetched = TRUE;
        return $this->validate() ? $this->options->value : FALSE;
    }
    /**
     * Prepare options
     * @return type
     */
    public function prepareOptions() {
        $this->options->checked = $this->options->value ? 'checked' : '';
        parent::prepareOptions();
        $this->options->text = $this->options->label;
        $this->options->label = NULL;
        return $this->options;
    }
}
