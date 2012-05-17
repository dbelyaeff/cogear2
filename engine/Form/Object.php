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

    public $options = array(
        'name' => '',
        'method' => 'POST',
        'action' => '',
        'class' => '',
        'enctype' => self::ENCTYPE_MULTIPART,
        'template' => 'Form.form',
        'prefix' => 'form',
        'ajax' => FALSE,
    );
    protected $is_init;
    protected $errors = 0;
    protected $counter = 0;
    public $ajaxed;
    public $result;

    /**
     * Rendered form code
     * @var string
     */
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
        'editor' => 'Form_Element_Textarea',
        'hidden' => 'Form_Element_Hidden',
        'radio' => 'Form_Element_Radio',
        'checkbox' => 'Form_Element_Checkbox',
        'select' => 'Form_Element_Select',
        'hidden' => 'Form_Element_Hidden',
        'submit' => 'Form_Element_Submit',
        'delete' => 'Form_Element_Delete',
        'button' => 'Form_Element_Button',
        'file' => 'Form_Element_File',
        'image' => 'Form_Element_Image',
        'span' => 'Form_Element_Span',
        'div' => 'Form_Element_Div',
        'tab' => 'Form_Element_Tab',
        'group' => 'Form_Element_Group',
        'fieldset' => 'Form_Element_Fieldset',
        'link' => 'Form_Element_Link',
        'title' => 'Form_Element_Title',
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
        $this->elements = new Core_ArrayObject();
        if (is_string($options)) {
            if (!$config = Config::read(Gear::preparePath($options, 'forms') . EXT)) {
                return error(t('Cannot read form config <b>%s</b>.', '', $options));
            } else {
                $options = $config;
            }
        } else {
            $options = Core_ArrayObject::transform($options);
        }
        parent::__construct($options);
        event('form.load', $this);
        event('form.load.' . $this->name, $this);
    }

    /**
     * Add element
     *
     * @param string $name
     * @param array $options
     */
    public function addElement($name, $config = array()) {
        !($config instanceof Core_ArrayObject) && $config = new Core_ArrayObject($config);
        $config->name = $name;
        $config->form = $this;
        if (!$config->order) {
            $this->counter++;
            $config->order = $this->counter;
        }
        if ($config->access !== FALSE) {
            if (isset(self::$types[$config->type]) && class_exists(self::$types[$config->type])) {
                $this->elements->$name = new self::$types[$config->type]($config);
            }
        }
    }

    /**
     * Magic __get method
     *
     * @param type $name
     */
    public function __get($name) {
        if ($this->elements->$name) {
            return $this->elements->$name;
        }
        return parent::__get($name);
    }

    /**
     * Initialize elements
     */
    public function init() {
        if ($this->is_init)
            return;
        event('form.init', $this);
        event('form.init.' . $this->name, $this);
        foreach ($this->options->elements as $name => $config) {
            $this->addElement($name, $config);
        }
        if ($this->callback && $callback = Cogear::prepareCallback($this->callback)) {
            if ($this->result = $this->result()) {
                call_user_func_array($callback, array($this));
            }
        }
        $this->is_init = TRUE;
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
        event('form.attach', $this);
        $this->setValues($this->object);
    }

    /**
     * Set values for fields
     *
     * @param array $data
     */
    public function setValues($data) {
        $this->is_init OR $this->init();
        if ($data instanceof Object) {
            $data = $data->object;
        }
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
        // Define if form is requested via ajaxed
        $this->ajaxed = $this->options->ajax && Ajax::is() && cogear()->input->post('ajaxed') === $this->getId();
        event('form.result.before', $this);
        $method = strtolower($this->options->method);
        $result = array();
        $is_valid = TRUE;
        if (cogear()->input->$method()) {
            foreach ($this->elements as $name => $element) {
                $value = $element->result();
                if ($value !== FALSE) {
                    $result[$name] = $value;
                } else {
                    $is_valid = FALSE;
                }
            }
        }
        event('form.result.after', $this, $is_valid, $result);
        if ($this->ajaxed) {
            $data = array();
            $data['success'] = $is_valid && $result;
            if ($data['success']) {
                $data['result'] = $result;
            } else {
                foreach ($this->elements as $key => $element) {
                    $element->errors->count() > 0 && $data['errors'][$key] = $element->errors;
                }
                $ajax = new Ajax();
                $ajax->json($data);
            }
        }
        return $is_valid && $result ? Core_ArrayObject::transform($result) : FALSE;
    }

    /**
     * Provide id for HTML form
     *
     * @return string
     */
    public function getId() {
        return $this->options->prefix . self::SEPARATOR . $this->options->name;
    }

    /**
     * Render form
     */
    public function render() {
        $this->init();
        event('form.render', $this);
        $this->elements->uasort('Core_ArrayObject::sortByOrder');
        $tpl = new Template($this->options->template);
        $id = $this->getId();
        $this->options->ajaxed && $this->options->class = trim($this->options->class . ' ajax');
        $this->action OR $this->options->action = l(cogear()->router->getUri());
        $tpl->form = $this;
        $this->options->id = $this->getId();
        $tpl->options = $this->options;
        $this->code = $tpl->render();
        event('form.render.after', $this);
        return $this->code;
    }

    /**
     *  Add error
     */
    public function error($error) {
        $this->errors++;
    }

    /**
     * Get number of errors
     *
     * @return int
     */
    public function errors() {
        return $this->errors;
    }

}