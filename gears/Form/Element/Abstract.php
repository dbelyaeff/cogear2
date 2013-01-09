<?php

/**
 * Abstract form element
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Form_Element_Abstract extends Errors_Handler {

    protected $options = array(
        'name' => '',
        'label' => '',
        'description' => '',
        'placeholder' => '',
        'value' => NULL,
        'type' => 'input',
        'template' => 'Form/templates/input',
        'wrapper' => 'Form/templates/element',
        'render' => TRUE,
        'disabled' => FALSE,
        'filters' => array(),
        'validators' => array(),
        'class' => 'form-element',
    );
    protected $value;
    protected $errors = array();
    public $code = '';

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options) {
        parent::__construct($options);
        /**
         * Если вдруг элементы названы иначе
         */
        foreach ($this->options as $key => $option) {
            switch ($key) {
                case 'filter':
                    $this->options->filters = $this->options->filter;
                    break;
                case 'validate':
                    $this->options->validators = $this->options->validate;
                    break;
            }
        }
    }

    /**
     * Set value
     *
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Transform option
     *
     * array('Length') OR array(array('Length',0,1))
     *
     * @param callback $class
     * @param string   $suffix
     * @return  callback|NULL
     */
    public function isCallable($class, $suffix = 'Validate') {
        $args = array();
        if (is_array($class) OR $class instanceof ArrayObject) {
            $args = array_slice($class->toArray(), 1);
            $class = $class[0];
        }
        class_exists($class) OR $class = 'Form_' . $suffix . '_' . $class;
        if (!class_exists($class)) {
            return NULL;
        }
        $callback = array($class, $args);
        return class_exists($class) ? $callback : NULL;
    }

    /**
     * Filter value
     */
    public function filter() {
        foreach ($this->filters as $filter) {
            if ($callback = $this->isCallable($filter, 'Filter')) {
                array_unshift($callback[1], $this->value);
                $filter = new $callback[0]();
                $filter->init($this);
                $this->value = call_user_func_array(array($filter, 'filter'), $callback[1]);
            }
        }
    }

    /**
     * Validate value
     *
     * @return  boolean
     */
    public function validate() {
        $is_valid = TRUE;
        foreach ($this->validators as $validator) {
            if ($callback = $this->isCallable($validator, 'Validate')) {
                array_unshift($callback[1], $this->value);
                $validator = new $callback[0];
                $validator->init($this);
                if (!call_user_func_array(array($validator, 'validate'), $callback[1])) {
                    $is_valid = FALSE;
                }
            }
        }
        return $is_valid;
    }

    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        if ($this->options->disabled) {
            return $this->value ? $this->value : TRUE;
        }
        $method = strtolower($this->form->method);
        $this->value = cogear()->input->$method($this->name, $this->options->value);
        $this->filter();
        $result = $this->validate() ? $this->value : FALSE;
        return $result;
    }

    /**
     * Provide id for HTML form
     *
     * @return string
     */
    public function getId() {
        return $this->options->form->getId() . Form_Object::SEPARATOR . $this->options->name;
    }

    /**
     * Form and return HTML object options from object data
     *
     * @return array
     */
    public function prepareOptions() {
        $this->options->required = $this->validators && strpos($this->validators->__toString(), 'Required') !== FALSE;
        if ($this->getErrors()) {
            $this->options->class .= ' error';
        }
        $this->options->id = $this->getId() . '-element';
        if ($this->value) {
            $this->options->value = $this->value;
        } else {
            $this->value = $this->options->value;
        }
        $this->options->element = $this;
        return $this->options;
    }

    /**
     * Render element
     */
    public function render() {
        $this->prepareOptions();
        $tpl = new Template($this->options->template);
        $tpl->assign($this->options);
        $this->code = $tpl->render();
        event('form.element.render', $this);
        event('form.element.' . $this->options->type . '.render', $this);
        $this->decorate();
        return $this->code;
    }

    /**
     * Decorate elements
     */
    protected function decorate() {
        if ($this->options->wrapper) {
            event('form.element.decorate', $this);
            event('form.element.' . $this->options->type . '.decorate', $this);
            $tpl = new Template($this->options->wrapper);
            $tpl->assign($this->options);
            $tpl->code = $this->code;
            $this->code = $tpl->render();
        }
    }

}