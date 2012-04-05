<?php

/**
 * Cogear itself
 *
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
final class Cogear implements Interface_Singleton {

    /**
     * Instance
     *
     * @var object
     */
    private static $_instance;

    /**
     * Events
     */
    public $events = array();

    /**
     * Instances of active gears
     *
     * @var ArrayObject
     */
    public $gears;

    /**
     * Active gears
     *
     * @var array
     */
    private $active_gears = array();

    /**
     * All gears in folders
     * @var array
     */
    private $all_gears = array();

    /**
     * Installed gears
     * @var array
     */
    private $installed_gears = array();

    /**
     * Flag inditcates gears state
     * 
     * @var boolean 
     */
    private $gears_are_loaded;

    /**
     * Flag to update gears system caches
     * @var boolean
     */
    private $write_gears = FALSE;

    /**
     * Flag to update config file
     * 
     * @var boolean 
     */
    private $write_config = FALSE;

    /**
     * Stop current event executrion flag
     */
    public $stop_event = FALSE;

    const GEAR = 'Gear';

    /**
     * Constructor
     */
    private function __construct() {
        $this->gears = new Core_ArrayObject();
        $this->events = new Core_ArrayObject();
    }

    /**
     * After gears are loaded
     */
    public function afterLoad() {
        inline_js('site = "http://' . SITE_URL . '/";', 'head');
    }

    /**
     * Clone
     */
    private function __clone() {
        
    }

    /**
     * Get instance
     *
     * @return Cogear
     */
    public static function getInstance() {
        return self::$_instance instanceof self ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Register hooks for event
     *
     * @param   string  $event
     * @param   callback  $callback
     */
    public function hook($event, $callback, $position = NULL) {
        $args = func_get_args();
        $this->events->$event OR $this->events->$event = new Event();
        $args = array_slice($args, 3);
        $callback = new Callback($callback);
        $callback->setArgs($args);
        if ($position !== NULL) {
            $this->events->$event->inject($callback, $position);
        } else {
            $this->events->$event->append($callback);
        }
    }

    /**
     * Run event
     * @param string $name
     * @param mixed $arg_1
     * вЂ¦
     * @param mixed $arg_N
     * @return  boolean
     */
    public function event($name) {
        $result = new Core_ArrayObject();
        $args = func_get_args();
        $args = array_slice($args, 1);
        if ($this->events->$name) {
            foreach ($this->events->$name as $callback) {
                if ($this->events->$name->is_stopped())
                    continue;
                if ($data = $callback->call($args)) {
                    $result->append($data);
                }
            }
        }
        return $result;
    }

    /**
     * Magic get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if ($this->gears->$name) {
            return $this->gears->$name;
        }
        $ucname = ucfirst($name);
        if ($this->gears->$ucname) {
            return $this->gears->$ucname;
        }
        return NULL;
    }

    /**
     * Get config var
     *
     * @param   string  $name
     * @param   string  $default
     * @return  string
     */
    public function get($name = NULL, $default = NULL) {
        if ($name === NULL) {
            return $this->config;
        }
        $pieces = explode('.', $name);
        $size = sizeof($pieces);
        $current = $this->config;
        $depth = 1;
        foreach ($pieces as $piece) {
            if ($current->$piece) {
                if ($depth < $size && $current->$piece instanceof Core_ArrayObject) {
                    $current = $current->$piece;
                    $depth++;
                    continue;
                }
                return $current->$piece;
            } else {
                return $current->$piece ? $current->$piece : $default;
            }
        }
        return $default;
    }

    /**
     * Set config value
     *  
     * @param type $name
     * @param type $value 
     * @return  boolean
     */
    public function set($name, $value) {
        $pieces = explode('.', $name);
        $current = $this->config;
        $i = 0;
        $size = sizeof($pieces);
        foreach ($pieces as $piece) {
            if ($i < $size - 1) {
                if ($current->$piece && $current->$piece instanceof Core_ArrayObject) {
                    $current = $current->$piece;
                } else {
                    $current->$piece = new Core_ArrayObject();
                    $current = $current->$piece;
                }
            } else {
                $current->$piece = $value;
            }
            $i++;
        }
        $current = $value;
        $this->write_config = TRUE;
        return TRUE;
    }

    /**
     *  Load gears 
     */
    public function loadGears() {
        if ($this->gears_are_loaded)
            return;
        hook('exit', array($this, 'save'));
        if (DEVELOPMENT OR !$this->all_gears = $this->system_cache->read('gears/all', TRUE)) {
            $this->all_gears = array();
            if ($gears_paths = array_merge(find('*' . DS . self::GEAR . EXT), find('*' . DS . '*' . DS . self::GEAR . EXT))) {
                foreach ($gears_paths as $path) {
                    $gear = self::pathToGear($path);
                    $class = $gear . '_' . self::GEAR;
                    if ($gear == 'Core' OR !class_exists($class)) {
                        continue;
                    }
                    $reflection = new ReflectionClass($class);
                    if (!$reflection->isAbstract() && $reflection->isSubclassOf(self::GEAR)) {
                        $gear = strtolower($gear);
                        $this->all_gears[$gear] = $class;
                    }
                }
            }
            $this->system_cache->write('gears/all', $this->all_gears);
        }
        $this->installed_gears = $this->system_cache->read('gears/installed', TRUE);
        $this->active_gears = $this->system_cache->read('gears/active', TRUE);
        foreach ($this->all_gears as $gear => $class) {
            if (isset($this->active_gears[$gear])) {
                $this->chargeGear($gear, $class);
            } elseif (DEVELOPMENT && class_exists($class)) {
                $object = new $class;
                if ($object->info('type') == Gear::CORE) {
                    if ($this->chargeGear($gear, $class)) {
                        $this->active_gears[$gear] = $class;
                        $this->write_gears = TRUE;
                    }
                }
            }
        }
        $this->sortGears();
        foreach ($this->gears as $name => $gear) {
            $gear->init();
        }
        Template::bindGlobal('cogear', $this);
        event('gears.loaded', $this);
        $this->afterLoad();
        $this->gears_are_loaded = TRUE;
    }

    /**
     * Simple method that turn gears on during loading process.
     * 
     * @param   string $gear 
     * @param   string  $class
     * @param   object|NULL $object
     * @return boolean
     */
    public function chargeGear($gear, $class, $object = NULL) {
        if (!($this->gears->$gear instanceof Gear) &&
                class_exists($class)) {
            $object OR $object = new $class;
            if ($object->checkGear()) {
                $this->gears->$gear = $object;
            } else {
                unset($object);
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Get all avialable gears
     * 
     * @return array 
     */
    public function getAllGears() {
        return $this->all_gears;
    }

    /**
     * Get only active gears
     * 
     * @return array
     */
    public function getActiveGears() {
        return $this->active_gears;
    }

    /**
     * Install gear
     * 
     * @param string $gear
     */
    public function install($gear) {
        if (isset($this->all_gears[$gear]) && class_exists($this->all_gears[$gear])) {
            $object = new $this->all_gears[$gear];
            $object->install();
            $this->installed_gears[$gear] = $this->all_gears[$gear];
            $this->write_gears = TRUE;
        }
        return $this;
    }

    /**
     * Uninstall gear
     * 
     * @param string $gear
     */
    public function uninstall($gear) {
        if (isset($this->all_gears[$gear]) && class_exists($this->all_gears[$gear])) {
            $object = new $this->all_gears[$gear];
            $object->uninstall();
            if (isset($this->installed_gears[$gear])) {
                unset($this->installed_gears[$gear]);
            }
            $this->write_gears = TRUE;
        }
        return $this;
    }

    /**
     * Update gear
     * 
     * @param string $gear
     */
    public function update($gear) {
        if (isset($this->all_gears[$gear]) && class_exists($this->all_gears[$gear])) {
            $object = new $this->all_gears[$gear];
            $object->update();
        }
        return $this;
    }

    /**
     * Activate gear
     * 
     * @param string $gear
     */
    public function activate($gear) {
        if (isset($this->all_gears[$gear]) && class_exists($this->all_gears[$gear])) {
            if (!isset($this->installed_gears[$gear])) {
                $this->install($gear);
            }
            $object = new $this->all_gears[$gear];
            $object->activate();
            $this->gears->$gear = $object;
            $this->gears->$gear->active = TRUE;
            $this->active_gears[$gear] = $this->all_gears[$gear];
            $this->write_gears = TRUE;
        }
        return $this;
    }

    /**
     * Deactivate gear
     * @param string $gear
     */
    public function deactivate($gear) {
        if ($this->gears->$gear) {
            $this->gears->$gear->active = FALSE;
            $this->gears->$gear->deactivate();
            if (isset($this->active_gears[$gear])) {
                unset($this->active_gears[$gear]);
            }
            $this->write_gears = TRUE;
        }
        return $this;
    }

    /**
     * Extract gear name from path
     * 
     * @param string $path
     * @return string
     */
    public static function pathToGear($path) {
        $paths = array(
            'site' => SITE . DS . GEARS_FOLDER . DS,
            'gears' => GEARS . DS,
            'engine' => ENGINE . DS,
            'alt_engine' => ENGINE . DS . 'Core' . DS,
        );
        foreach ($paths as $explicit_path) {
            if (strpos($path, $explicit_path) !== FALSE) {
                $path = str_replace($explicit_path, '', $path);
                continue;
            }
        }
        $gear = str_replace(array(
            DS . pathinfo($path, PATHINFO_BASENAME),
            DS
                ), array(
            '',
            '_'
                ), $path);
        return $gear;
    }

    /**
     * Get security key
     *
     * @return string>
     */
    public static function key() {
        return getInstance()->config->key;
    }

    /**
     * Save current gears and config
     */
    public function save() {
        if ($this->write_gears) {
            $this->system_cache->write('gears/all', $this->all_gears);
            $this->system_cache->write('gears/installed', $this->installed_gears);
            $this->system_cache->write('gears/active', $this->active_gears);
        }
        if ($this->write_config) {
            $settings = new Config(SITE . DS . 'settings' . EXT);
            $this->config->differ($settings);
            $this->config->store(SITE . DS . 'config' . EXT);
        }
        event('clear');
    }

    /**
     * Sort gears by parameter
     *
     * @param	string $param
     */
    private function sortGears($param = 'order') {
        $method = 'sortBy' . ucfirst($param);
        if (method_exists('Core_ArrayObject', $method)) {
            $this->gears->uasort('Core_ArrayObject::' . $method);
        }
    }

    /**
     * Prepare gear name from class
     *
     * @param   string  $class
     * @return  string
     */
    public static function prepareGearNameFromClass($class) {
        $gear = str_replace('_Gear', '', $class);
        return $gear;
    }

    /**
     * Check for required gears
     *
     * @param   Gear $gear  Gear itself
     * @return  boolean
     */
    public function requiredCheck(Gear $gear) {
        if (!$required = $gear->info('required'))
            return TRUE;
        $errors = array();
        foreach ($required as $requirement) {
            $result = self::parseVersion($requirement);
            $size = sizeof($result);
            if (!$required_gear = $this->gears->$result[0]) {
                $errors[] = $requirement;
            } else {
                $version = $required_gear->info('version');
                if (3 == $size && !version_compare($version, $result[2], $result[1]) OR
                        2 == $size && !version_compare($version, $result[1], '>=')) {
                    $errors[] = $requirement;
                } else {
                    return TRUE;
                }
            }
        }
        $errors && systemError(t('Gear <b>%s</b> can\'t be loaded, due to the following requirements conditions: %s.', 'Loader', $gear->info('name'), '<b>' . implode('</b> ,<b>', $errors) . '</b>'), t('Gears requirements interruption', 'Loader'));
        return FALSE;
    }

    /**
     * Parse version from gear requirement string
     * @param      $text
     */
    public static function parseVersion($text) {
        return preg_split('[\s]', $text, 3, PREG_SPLIT_NO_EMPTY);
    }

}

function getInstance() {
    return Cogear::getInstance();
}

function cogear() {
    return Cogear::getInstance();
}

function event() {
    $cogear = getInstance();
    $args = func_get_args();
    return call_user_func_array(array($cogear, 'event'), $args);
}

function hook() {
    $cogear = getInstance();
    $args = func_get_args();
    return call_user_func_array(array($cogear, 'hook'), $args);
}

function config($name = NULL, $default_value = NULL) {
    $cogear = getInstance();
    return $cogear->get($name, $default_value);
}