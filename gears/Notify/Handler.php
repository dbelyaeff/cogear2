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
     * Добавление примечания
     *
     * @param type $notice
     */
    public function notice($notice) {
        $this->notices->append($notice);
    }

}