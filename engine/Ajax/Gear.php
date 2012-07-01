<?php

/**
 * Ajax gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage  	jQuery
 * @version		$Id$
 */
class Ajax_Gear extends Gear {

    protected $name = 'Ajax';
    protected $description = 'Handle ajax requests.';
    protected $order = 0;
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
        append('footer', '<div id="ajax-loader"><p>' . t('Loading…', 'Ajax') . '</p></div>');
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