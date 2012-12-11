<?php

/**
 * Menu
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Menu_Auto extends Menu_Object {

    protected $is_init;
    /**
     * Рендер
     *
     * @return string
     */
    public function render() {
        $this->init();
        return parent::render();
    }

    /**
     * Инициализация
     */
    public function init() {
        if ($this->is_init)
            return;
        foreach (cogear()->gears as $gear) {
            if (method_exists($gear, 'menu') && (access($gear->gear) && access($gear->gear.'.menu'))){
                call_user_func_array(array($gear, 'menu'), array($this->options->name, &$this));
            }
        }
        $this->is_init = TRUE;
    }

}

