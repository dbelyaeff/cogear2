<?php

/**
 * Abstract widget
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Widgets_Widget extends Options {//Db_Item {

//    protected $table = 'widgets';
//    protected $primary = 'id';
//    protected $defaults = array(
//        'class' => 'well',
//    );
    public $options = array(
        'render' => 'sidebar',
        'class' => 'well',
        'order' => 0,
        'cache_ttl' => 600,
    );
    protected $template = 'Widgets/templates/widget';
    public $code = '';

    /**
     * Construcotr
     */
//    public function __construct() {
//        parent::__construct();
//        $this->defaults = Core_ArrayObject::transform($this->defaults);
//    }

    /**
     * Overriding parent find method
     *
     * @return   mixed
     */
//    public function find() {
//        if ($result = parent::find()) {
//            $this->settings = unserialize($this->settings);
//            $this->mixer();
//            if ($this->object()->class && class_exists($this->object()->class)) {
//                $object = new $this->object()->class();
//                $object->object($result);
//                $this->object($object);
//                return $object;
//            }
//        }
//        return $result;
//    }

    /**
     * Mix settings with defaults
     */
//    public function mixer() {
//        $this->settings = $this->defaults->mix($this->settings);
//    }

    /**
     * Override parent findAll method
     *
     * @return  mixed
     */
//    public function findAll() {
//        if ($result = parent::findAll()) {
//            foreach ($result as $key => $value) {
//                if (class_exists($value->object()->class)) {
//                    $item = new $value->object()->class();
//                    $item->object($value);
//                    $result->$key = $item;
//                } else {
//                    $result->offsetUnset($key);
//                }
//            }
//            foreach ($result as $item) {
//                if ($item->settings) {
//                    $item->settings = unserialize($item->settings);
//                    $item->mixer();
//                }
//            }
//        }
//        return $result;
//    }

    /**
     * Override parent getData method
     *
     * @return arrau
     */
//    public function getData() {
//        if ($data = parent::getData()) {
//            if (isset($data['settings'])) {
//                $data['settings'] = serialize($data['settings']);
//            }
//            $data['class'] = $this->reflection->getName();
//        }
//        return $data;
//    }

    /**
     * Return info about widget
     *
     * @return array
     */
//    public static function info(){
//        return array(
//            'name' => '',
//            'description' => '',
//            'logo' => '',
//            'package' => 'system',
//        );
//    }
    /**
     * Render
     *
     * @return type
     */
    public function render(){
        return template($this->template,array('code'=>$this->code,'options'=>$this->options,'item'=>$this))->render();
    }

    /**
     * Show widget
     */
    public function show($region = NULL,$position = 0, $where = 0){
        if(!$region) $region = $this->options->render;
        return parent::show($region,$position,$where);
    }
}