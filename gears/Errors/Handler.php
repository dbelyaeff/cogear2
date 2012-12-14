<?php

/**
 * Класс для регистрации и вывода ошибок
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Errors_Handler extends Options {

    protected $errors;

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array(), $place = 0) {
        parent::__construct($options, $place);
    }

    /**
     * Регистрирует ошибку
     *
     * @param mixed $data
     */
    protected function error($data) {
        $this->errors OR $this->errors = new Core_ArrayObject();
        $this->errors->append($data);
    }

    /**
     * Возвращает зарегистрированные ошибки
     *
     * @return Core_ArrayObject|NULL
     */
    public function getErrors() {
        return $this->errors;
    }


}