<?php

/**
 * Харвестер — пожинатель скриптов и стилей
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Assets_Object extends Object implements Interface_Factory {

    /**
     * Сущности
     *
     * @var type
     */
    protected static $_instances = array();

    /**
     * Настройки
     *
     * @var type
     */
    protected $options = array(
        'driver' => 'Assets_Driver_JavaScript',
        'glue' => TRUE,
    );

    /**
     * Метод "фабрики", производящий эксземпляр объекта текущего класса
     *
     * @param string $name
     * @param array $options
     * @param string $class
     * @return object
     */
    public static function factory($name, $options = array(), $class = __CLASS__ ) {
//        $class = isset($options['driver']) && class_exists($options['driver']) ? $options['driver'] : 'Assets_Driver_JS';
        $options['name'] = $name;
        return isset(self::$_instances[$name]) ? self::$_instances[$name] : self::$_instances[$name] = new $class($options);
    }

    /**
     * Инициалиазация
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        try {
            $this->object(new $this->options->driver($options));
        } catch (Exception $e) {
            error($e->getMessage());
        }
    }

}