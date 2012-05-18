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

    const PARAMS = '?';
    const PATH = '/';

    /**
     * Init
     */
    public function init() {
        parent::init();
        if (Ajax::is()) {
            event('ajax.hashchange');
        }
        append('footer','<div id="ajax-loader"><p>'.t('Loading…','Ajax').'</p></div>');
    }

}