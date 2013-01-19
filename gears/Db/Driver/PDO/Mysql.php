<?php

/**
 * Драйвер базы данных для работы через PDO
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 */
class Db_Driver_PDO_Mysql extends Db_Driver_PDO {

    protected $driver = 'mysql';

    /**
     * Логгер. Сохраняет запрос в массив и возвращает количество запросов
     *
     * @param string $query
     * @return int
     */
    protected function log($query) {
        if (!$this->queries) {
            $this->PDO->query('SET NAMES utf8;');
        }
        array_push($this->queries, $query);
        return sizeof($this->queries);
    }

}