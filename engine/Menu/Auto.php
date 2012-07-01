<?php

/**
 * Menu
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Menu_Auto extends Menu_Object {

    protected $is_init;

    /**
     * Render
     *
     * @return string
     */
    public function render() {
        $this->init();
        return parent::render();
    }

    /**
     * Init
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

