<?php

/**
 * Виджет темы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Theme_Widget extends Db_ORM_Options {

    protected $table = 'widgets';
    protected $instance;

    /**
     * Инициализация виджета
     *
     * @return  Widget_Element_Abstract
     */
    public function init() {
        $class = $this->callback;
        if (class_exists($class)) {
            $this->instance = new $class($this->object()->options);
            $this->instance->object($this);
            return $this->instance;
        }
        return FALSE;
    }

    /**
     * Отображение
     */
    public function render() {
        if ($this->init()) {
            return template('Theme/templates/widget', array('widget' => $this, 'content' => $this->instance->render()))->render();
        }
        return '';
    }


}