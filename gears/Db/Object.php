<?php

/**
 * Объект базы даных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Database
 */
class Db_Object extends Object implements Interface_Factory {

    protected static $_instances = array();

    /**
     * Метод "фабрики", производящий эксземпляр объекта текущего класса
     *
     * @param string $name
     * @param array $options
     * @param string $class
     * @return object
     */
    public static function factory($name, $options = array(), $class = __CLASS__) {
        return isset(self::$_instances[$name]) ? self::$_instances[$name] : self::$_instances[$name] = new $class($options);
    }
    /**
     * Конструктор
     *
     * @param array $options
     * @param string $class
     */
    public function __construct($options = NULL) {
        parent::__construct();
        $options->driver && class_exists($options->driver) && $this->object(new $options->driver($options));
    }
    /**
     * Проверка строки соединения с базой данных
     *
     * @param type $dsn
     */
    public static function parseDSN($dsn){
        if(!filter_var($dsn,FILTER_VALIDATE_URL)){
            error(t('Неверно указана строка подключения к базе данных.'));
            return FALSE;
        }
        $result = new Core_ArrayObject(parse_url($dsn));
        $args = new Core_ArrayObject($result->query ? parse_str($result->query) : array());
        $config =  new Core_ArrayObject(array(
            'driver' => $args->driver ? $args->driver : config('database.driver'),
            'host' => $result->host,
            'base' => trim($result->path,'/'),
            'user' => $result->user,
            'pass' => $result->pass,
            'port' => $result->port,
            'prefix' => $args->prefix,
        ));
//        $db = self::factory('temp',$config,$config->driver);
        return $config;
    }

    /**
     * Получение ошибок драйвера
     *
     * @return array
     */
    public function getErrors(){
        return $this->object()->getErrors();
    }
}