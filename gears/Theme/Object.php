<?php

/**
 * Объект темы оформления
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
abstract class Theme_Object extends Gear {

    protected $theme;
    protected $template;
    protected static $defaults;

    /**
     * Конструктор
     *
     * @param SimpleXMLElement $xml
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->theme = $this->gear;
        $this->template = new Template(THEMES . DS . $this->theme . DS . 'templates' . DS . 'index'.EXT);
    }

    /**
     * Init
     *
     * With inheritance
     */
    public function init() {
        $parent = $this->reflection->getParentClass();
        if ($parent->name != 'Gear' && $parent->name != 'Theme') {
            $theme = new $parent->name;
            $theme->init();
        }
        parent::init();
    }

    /**
     * Настройки по умолчанию для всех тем
     *
     * @return SimpleXMLObject
     */
    public static function getDefaultSettings() {
        return self::$defaults ? self::$defaults : self::$defaults = new SimpleXMLElement(file_get_contents(GEARS . DS . 'Theme' . DS . 'default.xml'));
    }

    /**
     * Get theme screenshot
     *
     * @return type
     */
    public function getScreenshot() {
        return file_exists($this->dir . $this->screenshot) ? $this->folder . $this->screenshot : '/' . THEMES_FOLDER . '/Default/images/screenshot.png';
    }

    /**
     * Set or get current layout
     *
     * @param string $template
     * @return string
     */
    public function template($template) {
        strpos($template,'/') OR $template = THEMES . DS . $this->theme . DS . 'templates' . DS . $template;
        $this->template = new Template($template);
        return $this;
    }

    /**
     * Render theme
     */
    public function render() {
        $this->template->theme = $this;
        cogear()->response->object()->append($this->template->render());
    }

}