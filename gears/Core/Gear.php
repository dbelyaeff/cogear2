<?php

/**
 * Gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
abstract class Gear extends Object {

    /**
     * Gear name
     * @var string
     */
    protected $name;

    /**
     * Gear description
     * @var string
     */
    protected $description;

    /**
     * Gear version
     *
     * By default it equals Cogear version
     *
     * @var string
     */
    protected $version = '1.0';

    /**
     * Core version
     *
     * @var type
     */
    protected $core = '2.x';
    /**
     * If gear can be deactivated
     *
     * @var boolean
     */
    protected $is_core = FALSE;

    /**
     * Package
     *
     * @var string
     */
    protected $package = 'Core';

    /**
     * Gear authors name
     *
     * @var string
     */
    protected $author = 'Dmitriy Belyaev';

    /**
     * Gear email
     *
     * Contact email to resolve everything
     * @var string
     */
    protected $email = 'admin@cogear.ru';

    /**
     * Gear website
     *
     * @var string
     */
    protected $site = 'http://cogear.ru';

    /**
     * Path to class file
     *
     * @var string
     */
    protected $path;

    /**
     * Directory where gear is located
     * @var string
     */
    protected $dir;

    /**
     * Relative path to folder with class
     *
     * @var string
     */
    protected $folder;

    /**
     * Order in gear stack
     *
     * Value can be positive or negative to load after or before other gears.
     *
     * @var int
     */
    protected $order = 0;

    /**
     * Class reflection
     *
     * Metaclass that stores all the info about current class
     *
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Gear name
     *
     * How does it stored in $cogear->gears->$name
     *
     * @param   string
     */
    protected $gear;

    /**
     * Info about gear file
     *
     * @var SplFileInfo
     */
    protected $file;

    /**
     * Simple uri name
     * It can be set in configuration, but if empty — will be default gear_name
     *
     * @var string
     */
    protected $base;

    /**
     * If gear is requested by router
     * @var boolean
     */
    protected $is_requested;

    /**
     * Required gears [version is optoinal]
     *
     * array(
     *  'Phpinfo',
     *   // or with version
     *  'Phpinfo 1.1',
     *   // or even with condition
     *  'Phpinfo > 1.1',
     * )
     * @var array
     */
    protected $required = array();

    /**
     * Hooks
     *
     * @var array
     */
    protected $hooks = array();

    /**
     * Routes
     *
     * @var routes
     */
    protected $routes = array();

    /**
     * List of widgets classes for this gear
     *
     * @var array
     */
    public $widgets;

    /**
     * Constructor
     */
    public function __construct() {
        $this->reflection = new ReflectionClass($this);
        $this->getPath();
        $this->getDir();
        $this->getFolder();
        $this->getGear();
        $this->getBase();
        $this->getSettings();
        $this->file = new SplFileInfo($this->path);
    }

    /**
     * Initialize
     */
    public function init() {
        $this->routes[$this->base . ':maybe'] = 'index';
        $this->loadAssets();
        $this->hooks();
        $this->routes();
        event('gear.init', $this);
    }

    /**
     * Load assets
     */
    protected function loadAssets() {
        $scripts = $this->dir . DS . 'js';
        $styles = $this->dir . DS . 'css';
        is_dir($scripts) && $this->assets->addScriptsFolder($scripts);
        is_dir($styles) && $this->assets->addStylesFolder($styles);
    }

    /**
     * Set hooks
     */
    public function hooks() {
        foreach ($this->hooks as $event => $callback) {
            if (!is_array($callback)) {
                $callback = array($this, $callback);
            }
            hook($event, $callback);
        }
    }

    /**
     * Set routes
     *
     */
    public function routes() {
        foreach ($this->routes as $route => $callback) {
            if (!is_array($callback)) {
                $callback = array($this, $callback);
            }
            cogear()->router->bind($route, $callback);
        }
    }

    /**
     * Check gear to be ready for charge in chain
     *
     * @return  boolean
     */
    public function checkGear() {
        $result = TRUE;
        if (!$this->checkRequiredGears()) {
            $result = FALSE;
        }
        return $result;
    }

    /**
     * Check required gears
     *
     * Response codes
     *
     * 1 — gear is ok
     * 0 — gear is not installed
     * -1 — gear version is not compatible
     *
     * @return boolean
     */
    public function checkRequiredGears() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'gears' => new Core_ArrayObject(),
                ));
        if ($this->required) {
            foreach ($this->required as $required_gear) {
                $pieces = explode(' ', $required_gear);
                $gear_name = $pieces[0];
                $sizeof = sizeof($pieces);
                $gear = cogear()->gears->$gear_name;
                if (NULL === $gear) {
                    $result->gears->$gear_name = FALSE;
                    $result->success = FALSE;
                    continue;
                }
                $result->gears->$gear_name = TRUE;
                switch ($sizeof) {
                    case 3:
                        if (!version_compare($gear->version, $pieces[2], $pieces[1])) {
                            $result->gears->$gear_name = $pieces[2];
                            $result->success = FALSE;
                            continue;
                        }
                        break;
                    case 2:
                        if (!version_compare($gear->version, $pieces[1], ' >= ')) {
                            $result->gears->$gear_name = $pieces[1];
                            $result->success = FALSE;
                            continue;
                        }
                        break;
                }
            }
        }
        return $result;
    }

    /**
     * Get or set gear state
     *
     * @param type $value
     * @return int
     */
    public function status($value = NULL) {
        /**
         * State of Core gear cannot be changed
         */
        if ($this->is_core) {
            return 0;
        }
        $gears = config('gears');
        if (NULL === $value) {
            return $gears->{$this->gear} ? $gears->{$this->gear} : Gears::EXISTS;
        } else {
            $gears->{$this->gear} = $value;
            cogear()->set('gears', $gears);
        }
    }

    /**
     * Install
     */
    public function install() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'message' => t('Gear has been successfully installed!', 'Gears'),
                ));
        if ($this->status() != Gears::EXISTS) {
            $result->message = t('This gear has been already installed!', 'Gears');
            $result->success = FALSE;
        }
        $this->status(Gears::INSTALLED);
        return $result;
    }

    /**
     * Uninstall
     */
    public function uninstall() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'message' => t('Gear has been successfully uninstalled!', 'Gears'),
                ));
        if ($this->status() != Gears::INSTALLED) {
            $result->message = t('Cannot uninstall active gear!', 'Gears');
            $result->success = FALSE;
        }
        $this->status(Gears::EXISTS);
        return $result;
    }

    /**
     * Activate
     */
    public function enable() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'message' => t('Gear has been successfully enabled!', 'Gears'),
                    'code' => 1,
                    'gears' => new Core_ArrayObject(),
                ));
        if ($this->status() != Gears::INSTALLED) {
            $result->message = t('Cannot enable already enabled gear!', 'Gears');
            $result->success = FALSE;
        }
        $check = $this->checkRequiredGears();
        if (TRUE !== $check->success) {
            $gears_required = new Core_ArrayObject();
            $gears_incomp_version = new Core_ArrayObject();
            foreach ($check->gears as $gear => $code) {
                if (FALSE === $code) {
                    $gears_required->append('<b>' . t($gear, 'Gears') . '</b>');
                } else {
                    $gears_incomp_version->append('<b>' . t($gear, 'Gears') . ' ' . $code . '</b>');
                }
            }
            $result->message = '';
            $gears_required->count() && $result->message .= t('Following gears are required to be enabled: ', 'Gears') . $gears_required->toString(", ") . "\n";
            $gears_incomp_version->count() && $result->message .= t('Following gears are required to be specific version: ', 'Gears') . $gears_incomp_version->toString(", ");
            $result->success = FALSE;
        }
        $result->success && $this->status(Gears::ENABLED);
        return $result;
    }

    /**
     * Deactivate
     */
    public function disable() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'message' => t('Gear has been successfully disabled!', 'Gears'),
                    'depends' => new Core_ArrayObject(),
                ));
        if ($this->status() != Gears::ENABLED) {
            $result->message = t('Cannot disable inactive gear!', 'Gears');
            $result->success = FALSE;
        }
        foreach(cogear()->gears as $gear){
            $check = $gear->checkRequiredGears();
            if($check->gears->{$this->gear}){
                $result->depends->{$gear->gear} = TRUE;
            }
        }
        if($result->depends->count()){
            $result->success = FALSE;
            $result->message = t('Cannot disable gear becase of following dependencies: ','Gears').' <b>'.implode('</b>, <b>',array_keys($result->depends->toArray())).'</b>';
        }
        $result->success && $this->status(Gears::EXISTS);
        return $result;
    }

    /**
     * Get Gear Path
     *
     * @return string
     */
    protected function getPath() {
        return $this->path = $this->reflection->getFileName();
    }

    /**
     * Get Gear directory
     *
     * @return  string
     */
    protected function getDir() {
        if (!$this->path)
            $this->getPath();
        return $this->dir = dirname($this->path);
    }

    /**
     * Get Gear relative folder
     *
     * @return string
     */
    protected function getFolder() {
        if (!$this->dir)
            $this->getDir();
        $this->folder = str_replace(array(ROOT, DS), array('', '/'), $this->dir);
        return self::normalizePath($this->dir);
    }

    /**
     * Get Gear name
     *
     * @return  string
     */
    protected function getGear() {
        return $this->gear ? $this->gear : $this->gear = Gears::nameFromClass($this->reflection->getName());
    }

    /**
     * Get base name
     */
    protected function getBase() {
        $cogear = getInstance();
        $base = str_replace('_', '/', strtolower($this->gear));
        return $this->base ? $this->base : $this->base = $cogear->get($this->gear . '.base', $base);
    }

    /**
     * Get default settings
     */
    public function getSettings() {
        $path = $this->dir . DS . 'settings' . EXT;
        if (file_exists($path) && !config($this->gear)) {
            $this->config->load($path, $this->gear);
        }
    }

    /**
     * Get gear widgets
     *
     * @return  array
     */
//    public function widgets() {
//        $this->widgets = new Core_ArrayObject($this->widgets);
//        $dir = $this->dir . DS . 'Widget';
//        if (is_dir($dir) && $files = glob($dir . DS . '*' . EXT)) {
//            foreach ($files as $file) {
//                $class = str_replace(array(EXT, dirname($this->dir) . DS, DS), array('', '', '_'), $file);
//                !in_array($class, $this->widgets->toArray()) && $this->widgets->append($class);
//            }
//        }
//        return $this->widgets;
//    }

    /**
     * Normalize relative path
     *
     * For example, under windows it look like \cogear\Theme\Default\, but wee need good uri to load css, js or anything else.
     * It transorm that path to /cogear/Theme/Default/.
     * @param   string  $path
     * @return  string
     */
    public static function normalizePath($path) {
        $path = str_replace(DS, '/', $path);
        return $path;
    }

    /**
     * Get gear name by path
     *
     * @param   string  $path
     * @return  string|boolean  Gear name or FALSE if path is not correct.
     */
    public static function getNameFromPath($path) {
        foreach (array(GEARS, ENGINE) as $dir) {
            if (strpos($path, $dir) !== FALSE) {
                is_file($path) && $path = dirname($path);
                $path = str_replace($dir, '', $path);
                $path = trim($path, DS);
                $pieces = explode(DS, $path);
                $gear_folder = '';
                foreach ($pieces as $piece) {
                    $gear_folder .= $piece . DS;
                    $gear_name = str_replace(DS, '_', trim($gear_folder, DS));
                    $gear_class = $gear_name . '_Gear';
                    if (file_exists($dir . DS . $gear_folder . DS . 'Gear' . EXT) && class_exists($gear_class)) {
                        return $gear_name;
                    }
                }
            }
        }
        return FALSE;
    }

    /**
     * Prepare path
     *
     * Errors.index = Core/Errors/$dir/index
     *
     * @param	string	$name
     * @param   string  $dir
     * @param   string  $default
     * @return	string  Path without file extension
     */
    public static function preparePath($name, $dir = '', $default = 'index') {
        if ($pieces = preg_split('#[\s><.]#', $name, -1, PREG_SPLIT_NO_EMPTY)) {
            if (sizeof($pieces) == 1) {
                array_push($pieces, $default);
            }
            $gear = array_shift($pieces);
            $cogear = getInstance();
            if ($cogear->gears->$gear) {
                $gear_dir = $cogear->gears->$gear->dir;
                $file_name = implode(DS, $pieces);
                return $path = $gear_dir . DS . $dir . DS . $file_name;
            }
        }
        return NULL;
    }

    /**
     * Notify gear that it's requested by uri
     */
    public function request() {
        $this->is_requested = TRUE;
        if (FALSE === event('gear.request', $this)->check()) {
            return;
        }
    }

    /**
     * Dispatcher
     * @param string $action
     */
    public function index() {
        if (!$args = func_get_args()) {
            $args[] = 'index';
        }
        if (!event('gear.dispatch', $this, $args)->check()) {
            return;
        }
        if (method_exists($this, $args[0] . '_action')) {
            call_user_func_array(array($this, $args[0] . '_action'), array_slice($args, 1));
        } elseif (method_exists($this, 'index_action')) {
            call_user_func_array(array($this, 'index_action'), $args);
        } else {
            event('404');
        }
        if (!event('gear.dispatch.after', $this, $args)->check()) {
            return;
        }
    }

}