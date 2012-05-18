<?php

/**
 * Theme Object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Theme
 * @version		$Id$
 */
abstract class Theme_Object extends Gear {

    protected $name = 'Theme';
    protected $description = 'Theme for cogear.';
    protected $order = 100;
    protected $package = 'Themes';
    protected $screenshot = '/img/screenshot.png';
    private static $is_rendered = FALSE;
    public static $layout = 'index';
    public $theme;
    protected $template;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->theme = Theme_Gear::classToTheme($this->gear);
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
     * Activate
     */
    public function activate() {
        $cogear = cogear();
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
    public function layout($template) {
        $this->layout = $template;
        return $this;
    }

    /**
     * Render theme
     */
    public function render() {
        $this->input->get('splash') !== NULL && self::$layout = 'splash';
        $this->template = new Template($this->theme . '.' . self::$layout);
        $this->template->theme = $this;
        cogear()->response->adapter->append($this->template->render());
    }

    /**
     * Get theme name by path
     *
     * @param   string  $path
     * @return  string|boolean  Gear name or FALSE if path is not correct.
     */
    public static function getNameFromPath($path) {
        foreach (array(SITE . DS . THEMES_FOLDER, THEMES) as $dir) {
            if (strpos($path, $dir) !== FALSE) {
                is_file($path) && $path = dirname($path);
                $path = str_replace($dir, '', $path);
                $path = trim($path, DS);
                $pieces = explode(DS, $path);
                $gear_folder = '';
                foreach ($pieces as $piece) {
                    $gear_folder .= $piece . DS;
                    $gear_name = str_replace(DS, '_', trim($gear_folder, DS));
                    $gear_class = $gear_name . '_Theme';
                    if (file_exists($dir . DS . $gear_folder . DS . 'Theme' . EXT) && class_exists($gear_class)) {
                        return $gear_name;
                    }
                }
            }
        }
        return FALSE;
    }

}