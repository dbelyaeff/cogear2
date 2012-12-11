<?php

/**
 * Cogear itself
 *
 *
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

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
     * Flag to update config file
     *
     * @var boolean
     */
    private $write_config = FALSE;

    /**
     * Stop current event execution flag
     */
    public $stop_event = FALSE;

    /**
     * Settings and config
     */
    public $site;
    public $config;
    public $system_cache;

    /**
     * Конструктор
     */
    private function __construct() {
        $this->gears = new Core_ArrayObject();
        $this->events = new Core_ArrayObject();
    }

    /**
     * Load Cogear
     */
    public function load() {
        $this->site = new Config(ROOT . DS . 'site' . EXT);
        $this->config = new Config(ROOT . DS . 'config' . EXT);
        $this->system_cache = new Cache_Object(array('path' => CACHE . DS . 'system'));
        defined('SITE_URL') OR define('SITE_URL', config('site.url'));
        if (strpos(SITE_URL, '/') && !defined('FOLDER')) {
            $array = explode('/', SITE_URL, 2);
            $folder = array_pop($array);
            define('FOLDER', $folder);
        }
        hook('ignite', array($this, 'loadGears'));
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
        $this->events->$event OR $this->events->$event = new Event($event);
        $callback = new Callback($callback);
        $args = func_get_args();
        if (sizeof($args) > 3) {
            $callback->setArgs(array_slice($args, 3));
        }
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
        $args = func_get_args();
        $args = array_slice($args, 1);
        return $this->events->$name ? $this->events->$name->run($args) : new Event($name);
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
     *  Load gears
     */
    public function loadGears() {
        $this->gears = new Gears($this->site->gears->extend($this->config->gears));
        foreach ($this->gears as $key=>$gear) {
            $check = $gear->checkRequiredGears();
            if ($check->success) {
                $gear->init();
            }
            else {
                $gear->disable();
            }
        }
    }

    /**
     * Get config
     *
     * @param type $name
     * @param type $default
     * @return  mixed
     */
    public function get($name, $default = NULL) {
        $result = $this->site->get($name);
        if (NULL !== $result) {
            return $result;
        }
        $result = $this->config->get($name);
        if (NULL !== $result) {
            return $result;
        }
        return $default;
    }

    /**
     * Set config
     *
     * @param type $name
     * @param type $value
     * @return  mixed
     */
    public function set($name, $value) {
        return $this->config->set($name, $value);
    }

//    /**
//     * Check for required gears
//     *
//     * @param   Gear $gear  Gear itself
//     * @return  boolean
//     */
//    public function requiredCheck(Gear $gear) {
//        if (!$required = $gear->info('required'))
//            return TRUE;
//        $errors = array();
//        foreach ($required as $requirement) {
//            $result = self::parseVersion($requirement);
//            $size = sizeof($result);
//            if (!$required_gear = $this->gears->$result[0]) {
//                $errors[] = $requirement;
//            } else {
//                $version = $required_gear->info('version');
//                if (3 == $size && !version_compare($version, $result[2], $result[1]) OR
//                        2 == $size && !version_compare($version, $result[1], '>=')) {
//                    $errors[] = $requirement;
//                } else {
//                    return TRUE;
//                }
//            }
//        }
//        $errors && systemError(t('Gear <b>%s</b> can\'t be loaded, due to the following requirements conditions: %s.', 'Loader', $gear->info('name'), '<b>' . implode('</b> ,<b>', $errors) . '</b>'), t('Gears requirements interruption', 'Loader'));
//        return FALSE;
//    }

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