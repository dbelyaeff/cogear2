<?php

/**
 * Menu 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Menu_Object extends Stack {

    protected $template = 'Menu.menu';
    protected $base_uri;
    public $position = 0;

    /**
     * Constructor
     *  
     * @param string $template 
     */
    public function __construct($name, $template = NULL, $base_uri = NULL) {
        parent::__construct($name);
        cogear()->menu->register($name, $this);
        $template && $this->template = $template;
        $this->base_uri = rtrim(parse_url($base_uri ? $base_uri : Url::link(), PHP_URL_PATH), '/') . '/';
    }

    /**
     * Get base url
     * 
     * @return string 
     */
    public function getBaseUri() {
        return $this->base_uri;
    }

    /**
     * Set active element
     * 
     * @param string $uri
     */
    public function setActive($uri = NULL) {
        if (!$this->count()) {
            return;
        }
        $cogear = getInstance();
        $uri OR $uri = $cogear->router->getUri();
        $root = trim($this->base_uri, '/');
        $uri = trim(str_replace($root, '', $uri), '/');
        $pieces = explode('/', trim($uri, '/'));
        while ($pieces) {
            $uri = implode('/', $pieces);
            foreach (array($uri, '/' . $uri . '/') as $path) {
                if ($this->{$path}) {
                    $this->{$path}->active(TRUE);
                }
            }
            array_pop($pieces);
        }
    }

    /**
     * Magic __set method
     *
     * @param	string
     * @param	mixed
     */
    public function __set($path, $value) {
        $element = new Menu_Item($path, $value, $this->position++, $this->base_uri);
        $this->add($path, $element);
    }

    /**
     * Magic __get method
     * 
     * @param type $name 
     */
    public function __get($name) {
        $name = trim($name, '/');
        $vars = new Core_ArrayObject(array($name, '/' . $name, $name . '/', '/' . $name . '/'));
        foreach ($vars as $name) {
            if ($this->offsetExists($name)) {
                return $this->offsetGet($name);
            }
        }
        return NULL;
    }

    /**
     * Add item to menu
     * 
     * @param string $path
     * @param Menu_Item $item 
     */
    public function add($path, Menu_Item $item) {
        $this->offsetSet($path, $item);
    }

    /**
     * Get menu name
     * 
     * @return string 
     */
    public function getName() {
        return preg_replace('#([^a-z-]+)#imsU', '-', $this->name);
    }

    /**
     * Mix current menu into another
     *  
     * @param string $name
     * @param string $place 
     */
    public function mixWith($menu, $name, $place = NULL) {
        $this->uasort('Core_ArrayObject::sortByOrder');
        $i = 1;
        if ($place && $menu->{$place}) {
            $position = $menu->{$place}->order;
        }
        else $position = $menu->position;
        foreach ($this as $path => $item) {
            $menu->add($path, new Menu_Item($path, $item->value, (float) ($position . '.' . $i++), $item->getBaseUri()));
        }
    }

    /**
     * Render menu
     * 
     * @param string $glue
     * @return string
     */
    public function render($template = '') {
        $template OR $template = $this->template;
        event('menu.' . $this->name, $this);
        if ($this->count()) {
            $this->uasort('Core_ArrayObject::sortByOrder');
            $this->setActive();
            $tpl = new Template($template);
            $tpl->menu = $this;
            return $tpl->render();
        }
        return NULL;
    }

    /**
     * Show menu
     */
    public function output() {
        echo $this->render();
    }

}
