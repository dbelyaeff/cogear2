<?php

/**
 * Класс Шестеренок
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Gears extends Options {

    const GEAR = 'Gear';
    const DISABLED = 0;
    const ENABLED = 1;
    const CORE = 2;

    public static $defaults;
    public $options = array(
        // Проверять ли на совместимость шестерёнки
        'check' => TRUE,
        // Удалять ли те, которые проверку не прошли
        'remove' => TRUE,
        // Сортировать ли по свойству конфига order
        'sort' => TRUE,
        // Превращать ли конфиги в объекты шестерёнок
        'charge' => TRUE,
    );

    /**
     * Конструктор
     * @param string|array|Core_ArrayObject $path
     */
    public function __construct($gears = NULL, $options = array()) {
        parent::__construct($options);
        if (is_array($gears) OR $gears instanceof Core_ArrayObject) {
            $this->load($gears);
        } else if (is_string($gears) && is_dir($gears)) {
            $this->loadDir($gears);
        }
        $this->init();
    }
    /**
     * Волшебный метод __get
     *
     * Иногда возникает потребность обратиться к шестерёнке с маленькой буквы.
     *
     * Например, $gears->access вместо $gears->Access
     *
     * @param string $name
     * @return  mixed
     */
    public function __get($name){
        $ucname = ucfirst($name);
        if($this->offsetExists($ucname)){
            return $this->offsetGet($ucname);
        }
        return NULL;
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
            // Если в массиве у нас названия шестерёнок, а не пути к конфигам — правим
            strpos($gear, 'gear.xml') OR $gear = GEARS . DS . $gear . DS . 'gear.xml';
            if (file_exists($gear)) {
                $xml = new XmlConfig($gear);
                $gear = $xml->attributes()->name->__toString();
                $this->offsetSet($gear, $xml->parse());
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
        $this->load($gears);
    }

    /**
     * Поиск шестеренок в директории.
     *
     * @param string $dir
     */
    public function find($dir) {
        $gears = glob($dir . DS . '*' . DS . 'gear.xml');
        return $gears;
    }

    /**
     * Фильтрация шестерёнок. Активные или неактивные, или шестерёнки ядра
     *
     * @param int $type
     */
    public function filter($type = Gears::ENABLED) {
        $gears = new self();
        foreach ($this as $gear => $object) {
            if ($type == Gears::ENABLED && NULL !== cogear()->config->gears->findByValue($gear)) {
                $gears->offsetSet($gear, $object);
            } else if ($type == Gears::CORE && NULL !== cogear()->site->gears->findByValue($gear)) {
                $gears->offsetSet($gear, $object);
            } else if ($type == Gears::DISABLED && NULL === cogear()->site->gears->findByValue($gear) && NULL === cogear()->config->gears->findByValue($gear)) {
                $gears->offsetSet($gear, $object);
            }
        }
        return $gears;
    }

    /**
     * Инициализирует шестерёнки.
     *
     * @param boolean $check Проверять ли шестерёнки на требования и совместимость
     */
    public function init() {
        // Проверка на совместимость
        if ($this->options->check) {
            // Внимание! Важно проверить несколько раз. Потому что зависимости могут быть у шестерёнок, идущих друг после друга.
//            $this->check();
//            $this->check();
//            $this->check();
        }
//        debug($this);
//        die();
        $this->options->sort && $this->uasort('Core_ArrayObject::sortByOrder');
        if ($this->options->charge) {
            foreach ($this as $gear => $options) {
                $class = $gear . '_' . self::GEAR;
                if (class_exists($class)) {
                    $this->$gear = new $class($options);
                }
                else {
                    $this->offsetUnset($gear);
                }
            }
        }
    }

    /**
     * Проверка требований и несовместимостей шестерёнок
     */
    public function check() {
        $remove = new Core_ArrayObject();
        foreach ($this as $name => $info) {
            if ($info->required) {
                $info->required->success = TRUE;
                foreach ($info->required->gear as $req_gear) {
                    $req_gear->success = TRUE;
                    if ($this->offsetExists($req_gear->name)) {
                        if ($req_gear->disabled) {
                            $req_gear->success = -2;
                            $info->required->success = FALSE;
                        } else if ($req_gear->version) {
                            if ($req_gear->condition && !version_compare($this->{$req_gear->name}->version, $req_gear->version, $req_gear->condition)) {
                                $req_gear->success = -1;
                                $info->required->success = FALSE;
                            } else if (version_compare($this->{$req_gear->name}->version, $req_gear->version, ' < ')) {
                                $req_gear->success = 0;
                                $info->required->success = FALSE;
                            }
                        }
                    } else {
                        $req_gear->success = FALSE;
                        $info->required->success = FALSE;
                    }
                    if (FALSE === $info->required->success) {
                        $remove->append($name);
                    }
                }
            }
        }
        if ($this->options->remove) {
            foreach ($remove as $gear) {
                $this->offsetUnset($gear);
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