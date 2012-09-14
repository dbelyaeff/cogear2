<?php

/**
 * Gears class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Gears extends Core_ArrayObject {

    const GEAR = 'Gear';
    const CORE = 0;
    const EXISTS = 1;
    const INSTALLED = 2;
    const ENABLED = 3;

    /**
     * Constructor
     * @param type $path
     */
    public function __construct($path = NULL) {
        if (is_dir($path)) {
            $this->loadDir($path);
            $this->uasort('Core_ArrayObject::sortByOrder');
        }
    }

    /**
     * Load gears from dir by path
     *
     * @param type $path
     */
    public function loadDir($path) {
        $gears = $this->find($path);
        foreach ($gears as $gear) {
            $this->charge($gear);
        }
    }

    /**
     * Find gears in direcotry
     *
     * @param string $dir
     */
    public function find($dir) {
        $gears = glob($dir . DS . '*' . DS . self::GEAR . EXT);
        if ($dive = glob($dir . DS . '*' . DS . '*' . DS . self::GEAR . EXT)) {
            $gears = array_merge($gears, $dive);
        }
        return $gears;
    }

    /**
     * Simple method that turn gears on during loading process.
     *
     * @param   string $path
     * @return boolean
     */
    public function charge($path) {
        $gear = self::pathToGear($path);
        $class = $gear . '_' . self::GEAR;
        if ($class != 'Core_Gear') {
            $this->$gear = new $class;
        }
    }

    /**
     * Filter gears
     *
     * @param int $type
     * @return Gears
     */
    public function filter($type = self::CORE, $include_core = TRUE) {
        $result = new self();
        foreach ($this as $key => $gear) {
            if ($include_core && $gear->is_core) {
                $result->$key = $gear;
                continue;
            }
            switch ($type) {
                case self::INSTALLED:
                    if ($gear->status() == self::INSTALLED) {
                        $result->$key = $gear;
                    }
                    break;
                case self::ENABLED:
                    if ($gear->status() == self::ENABLED) {
                        $result->$key = $gear;
                    }
                    break;
                case self::EXISTS:
                    if ($gear->status() == self::EXISTS && !$gear->is_core) {
                        $result->$key = $gear;
                    }
                    break;
            }
        }
        return $result;
    }

    /**
     * Extract gear name from path
     *
     * @param string $path
     * @return string
     */
    public static function pathToGear($path) {
        $paths = array(
            'gears' => GEARS . DS,
            'alt_gears' => GEARS . DS . 'Core' . DS,
            'themes' => THEMES . DS,
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
     * Sort gears by parameter
     *
     * @param	string $param
     */
    private function sortGears($param = 'order') {
        $method = 'sortBy' . ucfirst($param);
        if (method_exists('Core_ArrayObject', $method)) {
            $this->uasort('Core_ArrayObject::' . $method);
        }
    }

    /**
     * Prepare gear name from class
     *
     * @param   string  $class
     * @return  string
     */
    public static function nameFromClass($class) {
        $gear = str_replace('_Gear', '', $class);
        return $gear;
    }

}