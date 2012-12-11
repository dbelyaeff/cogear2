<?php

/**
 * Шестеренка асинхронных запросов
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Ajax_Gear extends Gear {

    protected $hooks = array(
        'assets.js.global' => 'hookGlobalJS',
    );

    const PARAMS = '?';
    const PATH = '/';

    /**
     * Hook global JS
     */
    public function hookGlobalJS($cogear) {
        $cogear->settings->ajax = array(
            'showLoader' => config('Ajax.show.loader', FALSE),
        );
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        if (Ajax::is()) {
            event('ajax.hashchange');
        }
    }

}

/**
 * Shortcut for ajax
 */
function ajax($data = NULL) {
    $ajax = new Ajax();
    if ($data) {
        $ajax->append($data);
        $ajax->send();
    }
    return $ajax;
}

/**
 * Send json response
 *
 * @param type $data
 */
function json($data) {
    ajax()->json($data);
}