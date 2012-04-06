<?php

/**
 * Abstract form element
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Form_Element_Abstract extends Options {

    /**
     * Link to form instance
     *
     * @var object
     */
    protected $form;
    protected $errors = array();
    protected $wrapper = 'Form.element';
    public $code = '';

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options) {
        $this->filters = new Core_ArrayObject();
        $this->validators = new Core_ArrayObject();
        parent::__construct($options);
        $this->errors = new Core_ArrayObject();
    }

    /**
     * addFilter
     *
     * @param string $filter
     */
    public function addFilter($filter) {
        in_array($filter, $this->filters) OR $this->filters[] = $filter;
    }

    /**
     * addFilter
     *
     * @param string $validator
     */
    public function addValidator($validator) {
        in_array($validator, $this->validators) OR $this->validators[] = $validator;
    }

    /**
     * Add error
     *
     * @param   string  $error
     */
    public function addError($error) {
        $this->errors->findByValue($error) OR $this->errors->append($error);
        return FALSE;
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
        $this->value = isset($this->form->request[$this->name]) ? $this->form->request[$this->name] : $this->value;
        $this->is_fetched = TRUE;
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
        return $this->form->getId() . Form_Object::SEPARATOR . $this->name;
    }

    /**
     * Form and return HTML object options from object data
     * 
     * @return array
     */
    public function prepareOptions() {
        $this->options->required = $this->validators && $this->validators->findByValue('Required');
        $this->options->disabled OR $this->options->offsetUnset('disabled');
        $this->options->form = $this->form;
        $this->options->element = $this;
        return $this->options;
    }

    /**
     * Render element
     */
    public function render() {
        $this->prepareOptions();
        $this->decorate();
        event('Form.element.' . $this->type . '.render', $this);
        return $this->code;
    }

    /**
     * Decorate elements
     */
    protected function decorate() {
        if ($this->wrapper) {
            $tpl = new Template($this->wrapper);
            $tpl->code = $this->code;
            $this->code = $tpl->render();
        }
    }

}
