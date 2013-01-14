<?php

/**
 * Виджет темы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Theme_Widget extends Db_ORM {

    protected $table = 'widgets';
    protected $filters_in = array(
        'options' => array('serialize'),
    );
    protected $filters_out = array(
        'options' => array('unserialize'),
    );
    protected $instance;

    /**
     * Инициализация виджета
     *
     * @return  Widget_Element_Abstract
     */
    public function init() {
        $class = $this->callback;
        $this->instance = new $class($this->object()->options);
        $this->instance->object($this);
        return $this->instance;
    }

    /**
     * Отображение
     */
    public function render() {
        $this->instance OR $this->init();
        return template('Theme/templates/widget',array('widget'=>$this,'content' => $this->instance->render()))->render();
    }

}