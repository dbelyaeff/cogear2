<?php

/**
 * Класс Шестеренок
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Gears extends Core_ArrayObject {

    const GEAR = 'Gear';
    const DISABLED = 0;
    const ENABLED = 1;

    public static $defaults;

    /**
     * Конструктор
     * @param string|array|Core_ArrayObject $path
     */
    public function __construct($gears = NULL) {
        if (is_array($gears) OR $gears instanceof Core_ArrayObject) {
            $this->load($gears);
        } else if (is_string($gears) && is_dir($path)) {
            $this->loadDir($path);
        }
        $this->uasort('Core_ArrayObject::sortByOrder');
    }

    /**
     * Настройки по умолчанию для всех шестеренок
     *
     * @return SimpleXMLObject
     */
    public static function getDefaultSettings() {
        return self::$defaults ? self::$defaults : self::$defaults = new SimpleXMLElement(file_get_contents(GEARS . DS . 'Core' . DS . 'default.xml'));
    }

    /**
     * Загрузка шестеренок из массива или Core_ArrayObject'а
     *
     * @param array|Core_ArrayObject $gears
     */
    public function load($gears = array()) {
        foreach ($gears as $gear) {
            $xml = GEARS . DS . $gear . DS . 'gear.xml';
            if (file_exists($xml)) {
                $this->charge($xml);
            }
        }
    }

    /**
     * Загрузка шестеренок по указанному пути
     *
     * @param string $path
     */
    public function loadDir($path) {
        $gears = $this->find($path);
        foreach ($gears as $gear) {
            $this->charge($gear);
        }
    }

    /**
     * Поиск шестеренок в директории.
     *
     * @param string $dir
     */
    public function find($dir) {
        $gears = glob($dir . DS . 'gear.xml');
        return $gears;
    }

    /**
     * Заряжает шестеренку
     *
     * @param   string $path
     * @return boolean
     */
    public function charge($xml) {
        $file = file_get_contents($xml);
        $config = new SimpleXMLElement($file);
        if ($config instanceof SimpleXMLElement) {
            $gear = $config->attributes()->name->__toString();
            $class = $gear . '_' . self::GEAR;
            if (class_exists($class)) {
                $this->offsetSet($gear, new $class($config));
            }
        }
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

}