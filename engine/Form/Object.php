<?php

/**
 * Form Manager
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Form_Object extends Object {

    protected $name;
    protected $prefix = 'form';
    protected $method = 'POST';
    protected $action;
    protected $ajax;
    protected $enctype = self::ENCTYPE_MULTIPART;
    protected $template = 'Form.form';
    public $request;
    protected $is_ajaxed;
    protected $initialized;
    public $tab_opened;
    public $code;
    /**
     * Elements config
     * @var array
     */
    public $elements = array();
    public static $types = array(
        'input' => 'Form_Element_Input',
        'text' => 'Form_Element_Input',
        'password' => 'Form_Element_Password',
        'textarea' => 'Form_Element_Textarea',
        'hidden' => 'Form_Element_Hidden',
        'radio' => 'Form_Element_Radio',
        'checkbox' => 'Form_Element_Checkbox',
        'select' => 'Form_Element_Select',
        'hidden' => 'Form_Element_Hidden',
        'submit' => 'Form_Element_Submit',
        'button' => 'Form_Element_Button',
        'file' => 'Form_Element_File',
        'image' => 'Form_Element_Image',
        'span' => 'Form_Element_Span',
        'div' => 'Form_Element_Div',
        'tab' => 'Form_Element_Tab',
    );
    protected $callback;
    /**
     * Helps to transform form names to jQuery-readable
     *
     * @const
     */
    const SEPARATOR = '-';
    /**
     * Constants
     *
     * @const
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART = 'multipart/form-data';

    /**
     * Constructor
     * 
     * @param string|array $options
     */
    public function __construct($options) {
        if (is_string($options)) {
            if (!$config = Config::read(Gear::preparePath($options, 'forms') . EXT)) {
                return error(t('Cannot read form config <b>%s</b>.', '', $options));
            } else {
                $options = $config;
            }
        }
        else {
            $options = Core_ArrayObject::transform($options);
        }
        parent::__construct($options,Options::SELF);
    }
    /**
     * Magic method to get form element
     * 
     * @param string $name 
     */
    public function __get($name){
        return $this->elements->$name;
    }
    /**
     * Add element
     * 
     * @param string $name
     * @param array $options 
     */
    public function addElement($name, $config = array()) {
        !($config instanceof Core_ArrayObject) && $config = new Core_ArrayObject($config);
        $config->type OR $config->type = 'input';
        $config->name = $name;
        $config->form = $this;
        if ($config->access === FALSE) {
            return;
        }
        if (isset(self::$types[$config->type]) && class_exists(self::$types[$config->type])) {
            $this->elements->$name = new self::$types[$config->type]($config);
        } else {
            $this->elements->offsetUnset($name);
        }
    }

    /**
     * Initialize elements
     */
    public function init() {
        if ($this->initialized)
            return;
        event('form.init', $this);
        event('form.' . $this->name.'.init', $this);
        $this->is_ajaxed = isset($_REQUEST['form']) && $_REQUEST['form'] == $this->name;
        $elements = array();
        foreach ($this->elements as $name => $config) {
            $this->addElement($name, $config);
        }
        $this->initialized = TRUE;
        if ($this->callback && $callback = Cogear::prepareCallback($this->callback)) {
            if ($result = $this->result()) {
                call_user_func_array($callback, array($result));
            } else {
                return $this->render();
            }
        }
    }

    /**
     * Get or set form object
     * 
     * Form elements values are set automatically
     * 
     * @param object $data 
     */
    public function attach($data) {
        parent::attach($data);
        event('form.'.$this->name.'.attach',$this);
        event('form.attach',$this);
        $this->init();
        $this->setValues($data);
    }

    /**
     * Set values for fields
     * 
     * @param array $data 
     */
    public function setValues($data) {
        foreach ($data as $key => $value) {
            $this->elements->$key && $this->elements->$key->setValue($value);
        }
    }

    /**
     * Result
     *
     * @return  NULL|Core_ArrayObject — filtered and validated data
     */
    public function result() {
        $this->init();
        event('form.result.before', $this);
        event('form.' . $this->name . '.result.before', $this);
        $this->request = $this->method == 'POST' ? $_POST : $_GET;
        $result = array();
        $is_valid = TRUE;
        if (sizeof($this->request) > 0) {
            foreach ($this->elements as $name => $element) {
                $value = $element->result();
                if ($value !== FALSE) {
                    $result[$name] = $value;
                } else {
                    $is_valid = FALSE;
                }
            }
        }
        if ($this->is_ajaxed) {
            $response = array();
            foreach ($this->elements as $name => $element) {
                if ($name == Ajax::get('element')) {
                    if ($result = $element->ajax()) {
                        $response[$name] = $result;
                    }
                }
            }
            event('form.ajax.before', $this, $response);
            event('form.' . $this->name . '.ajax.before', $this, $response);
            $response && Ajax::json($response);
        }
        event('form.result.after', $this, $is_valid, $result);
        event('form.' . $this->name . '.result.after', $this, $is_valid, $result);
        return $is_valid && $result ? Core_ArrayObject::transform($result) : FALSE;
    }

    /**
     * Provide id for HTML form
     *
     * @return string
     */
    public function getId() {
        return $this->prefix . self::SEPARATOR . $this->name;
    }

    /**
     * Render form
     */
    public function render() {
        $this->init();
        event('form.render', $this);
        event('form.' . $this->name . '.render', $this);
        $tpl = new Template($this->template);
        $id = $this->getId();
        $tpl->form = array(
            'id' => $id,
            'name' => $id,
            'method' => $this->method,
            'action' => $this->action,
            'enctype' => $this->enctype,
            'class' => 'form' . ($this->ajax ? ' ajaxed' : ''),
        );
        $tpl->elements = $this->elements;
        $this->code = $tpl->render();
        event('form.render.after', $this);
        event('form.' . $this->name . '.render.after', $this);
        return $this->code;
    }
}