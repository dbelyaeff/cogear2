<?php

/**
 * Объект виджета.
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
abstract class Theme_Widget_Abstract extends Object {

    /**
     * Настройки по умолчанию
     *
     * @var type
     */
    protected $options = array(
        'title' => '',
    );

    /**
     * Настройки
     *
     * @return  boolean TRUE если настройки сохранены успешно
     */
    abstract function settings();

    /**
     * Сохранение настроек
     *
     * @return  boolean
     */
    protected function save() {
        $widget = $this->object();
        $widget->object()->options = $this->options;
        return $widget->save();
    }

}