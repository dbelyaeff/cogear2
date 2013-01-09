<?php

/**
 * Form Manager
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Form_Object extends Object {

    protected $options = array(
        'name' => '',
        'method' => 'POST',
        'action' => '',
        'title' => '',
        'class' => 'form',
        'enctype' => self::ENCTYPE_MULTIPART,
        'template' => 'Form/templates/form',
        'prefix' => 'form',
        'ajax' => FALSE,
    );
    protected $is_init;
    protected $counter = 0;
    public $ajaxed;
    public $result;
    protected $defaults;

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
     * Конструктор
     *
     * @param string|array $options
     */
    public function __construct($options) {
        $this->elements = new Core_ArrayObject();
        if (is_string($options)) {
            $path = Gear::preparePath($options);
            if (!$config = Config::read($path)) {
                error(t('Не могу прочитать файл конфигурации формы по адресу <b>%s</b>.', $path));
                $options = $this->options;
            } else {
                $options = $config;
            }
        } else {
            $options = Core_ArrayObject::transform($options);
        }
        parent::__construct(Form::filterOptions($options));
        $this->defaults = new Config(cogear()->form->dir.DS.'defaults'.EXT);
        event('form.load', $this);
        event('form.load.' . $this->name, $this);
        $this->init();
    }
    /**
     * Фильтрация опций
     *
     * @param array $options
     */
    public static function filterOptions($options){
        // Если указаны напрямую элементы, значит данные по старому идут
        // Проверка на name указана для определения настроек формы, а не её элемента
        if(isset($options['elements']) && isset($options['name'])){
            return $options;
        }
        $results = new Core_ArrayObject();
        foreach($options as $key=>$value){
            if($key[0] == '#'){
                $real_key = substr($key,1);
                $results->$real_key = $value;
            }
            else {
                $results->elements OR $results->elements = new Core_ArrayObject();
                if(is_array($value) OR $value instanceof Core_ArrayObject){
                    $value = self::filterOptions($value);
                }
                $results->elements->$key = $value;
            }
        }
        return $results;
    }
    /**
     * Add element
     *
     * @param string $name
     * @param array $options
     */
    public function add($name, $config = array()) {
        !($config instanceof Core_ArrayObject) && $config = new Core_ArrayObject($config);
        if ($this->defaults->$name) {
            $this->defaults->$name->extend($config);
            $config = $this->defaults->$name;
        }
        if ($config->label && !$config->placeholder && in_array($config->type, array('text', 'input', 'textarea', 'password', 'editor'))) {
            $config->placeholder = t('Введите %s…', mb_strtolower($config->label, 'UTF8'));
        }
        $config->name = $name;
        if (strpos($name, '[]')) {
            $config->multiple = TRUE;
            $name = str_replace('[]', '', $name);
        }
        $config->form = $this;
        if (!$config->order) {
            $this->counter++;
            $config->order = $this->counter;
        }
        if (is_string($config->access)) {
            if ($this->object) {
                $config->access = access($config->access, $this->object);
            } else {
                $config->access = access($config->access);
            }
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
    private function init() {
        if ($this->is_init)
            return;
        event('form.init', $this);
        event('form.init.' . $this->options->name, $this);
        if ($this->options->elements) {
            foreach ($this->options->elements as $name => $config) {
                $this->add($name, $config);
            }
            if ($this->callback && $callback = Cogear::prepareCallback($this->callback)) {
                if ($this->result = $this->result()) {
                    call_user_func_array($callback, array($this));
                }
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
    public function object($data = NULL) {
        $result = parent::object($data);
        if ($data) {
            if ($data instanceof Object) {
                $data = $data->object();
            }
            foreach ($data as $key => $value) {
                $this->elements->$key && $this->elements->$key->setValue($value);
            }
            event('form.attach', $this, $data);
        }
        return $result;
    }

    /**
     * Result
     *
     * @return  NULL|Core_ArrayObject — filtered and validated data
     */
    public function result() {
        // Define if form is requested via ajaxed
        $this->ajaxed = $this->options->ajax && Ajax::is() && cogear()->input->post('ajaxed') === $this->getId();
        $method = strtolower($this->options->method);
        $result = array();
        $is_valid = TRUE;
        if (cogear()->input->$method() OR $_FILES) {
            foreach ($this->elements as $name => $element) {
                $value = $element->result();
                if ($value !== FALSE) {
                    $result[$name] = $value;
                } else {
                    $is_valid = FALSE;
                }
            }
        }
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
        if ($is_valid && $result) {
            $result = Core_ArrayObject::transform($result);
        }
        if (!event('form.result.' . $this->options->name, $this, $is_valid, $result)->check() OR
                !event('form.result', $this, $is_valid, $result)->check()) {
            return FALSE;
        }
        return $is_valid ? $result : FALSE;
    }

    /**
     * Provide id for HTML form
     *
     * @return string
     */
    public function getId() {
        return preg_replace('#[\._]#','-',$this->options->prefix . self::SEPARATOR . $this->options->name);
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

}