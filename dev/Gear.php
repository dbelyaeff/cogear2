<?php

/**
 * Шестрёнка
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Dev_Gear extends Gear {

    protected $hooks = array(
    );
    protected $routes = array(
    );
    protected $access = array(
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            case 'create':
                return TRUE;
                break;
        }
        return FALSE;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Hook menu
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {

        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($action = '', $subaction = NULL) {

    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function some_action($subaction = NULL) {

    }

}