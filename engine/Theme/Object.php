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
    protected $layout = 'index';
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
     * Theme admin page
     * 
     * @param type $action
     * @param type $subaction 
     */
    public function admin($action = NULL, $subaction = NULL) {
        $form = new Form('Admin.theme');

        if ($form->is_ajaxed) {
            if ($form->elements->logo->is_ajaxed) {
                $cogear->set('theme.logo', '');
            }
            if ($form->elements->favicon->is_ajaxed) {
                $cogear->set('theme.favicon', '');
            }
        } else {
            $form->setValues(array(
                'logo' => config('theme.logo'),
                'favicon' => config('theme.favicon'),
            ));
        }
        if ($result = $form->result()) {
            $result->logo && $cogear->set('theme.logo', $result->logo);
            $result->favicon && $cogear->set('theme.favicon', $result->favicon);
        }
        append('content', $form->render());
    }

    /**
     * Init
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
        Template::bindGlobal('theme', $this);
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
    public function layout($template = NULL) {
        return $template ? $this->layout = $template : $this->layout;
    }

    /**
     * Render theme
     */
    public function render($layout = NULL) {
        if ($this->is_rendered) {
            return;
        }
        $cogear = cogear();
        $this->template = new Template($this->theme . '.' . (defined('LAYOUT') ? LAYOUT : ($layout ? $layout : $this->layout)));
        $cogear->response->append($this->template->render());
        $this->is_rendered = TRUE;
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