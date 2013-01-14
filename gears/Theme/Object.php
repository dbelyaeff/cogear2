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
    protected $menu;

    /**
     * Настройки темы по умолчанию
     *
     * @var array
     */
    protected static $defaults;

    /**
     * Конструктор
     *
     * @param Config $config
     */
    public function __construct($config) {
        $defaults = self::getDefaultSettings();
        $defaults->extend($config);
        parent::__construct($defaults);
        $this->initRegions();
        $this->template = new Template(THEMES . DS . $this->theme . DS . 'templates' . DS . 'index' . EXT);
        $this->menu = new Menu(array(
                    'name' => 'theme',
                    'render' => FALSE,
                    'elements' => array(
                        array(
                            'label' => icon('download'),
                            'link' => l('/admin/theme/download') . '?themes=' . $this->theme,
                            'class' => 'btn btn-mini'
                        )
                    )
                ));
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
     * @return Config
     */
    public static function getDefaultSettings() {
        return self::$defaults ? self::$defaults : self::$defaults = new Config(cogear()->Theme->dir . DS . 'defaults' . EXT);
    }

    /**
     * Получение скриншоты бемы
     *
     * @return type
     */
    public function getScreenshot() {
        return file_exists($this->dir . DS . $this->screenshot) ? $this->dir . DS . $this->screenshot : cogear()->theme->dir . DS . $this->screenshot;
    }

    /**
     * Установка текущего шаблона темы
     *
     * @param string $template
     * @return string
     */
    public function template($template) {
        strpos($template, '/') OR $template = THEMES . DS . $this->theme . DS . 'templates' . DS . $template;
        $this->template = new Template($template);
        return $this;
    }

    /**
     * Отображение темы
     */
    public function render() {
        $this->template->theme = $this;
        cogear()->response->object()->append($this->template->render());
    }


}