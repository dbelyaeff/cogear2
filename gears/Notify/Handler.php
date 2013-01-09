<?php

/**
 * Управляющий замечаниями
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Notify_Handler extends Errors_Handler {

    protected $notices;
    /**
     * Конструктор
     *
     * @param array $options
     * @param int $place
     */
    public function __construct($options = array(), $place = 0) {
        $this->notices = new Core_ArrayObject();
        parent::__construct($options, $place);
    }

    /**
     * Добавление примечания
     *
     * @param string $notice
     */
    public function notice($notice) {
        $this->notices OR $this->notices = new Core_ArrayObject();
        $this->notices->append($notice);
    }

    /**
     * Возвращает примечания
     *
     * @return mixed
     */
    public function getNotices() {
        return $this->notices->count() ? $this->notices : NULL;
    }

}