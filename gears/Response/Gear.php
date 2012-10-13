<?php

/**
 * Response gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Response_Gear extends Gear {

    protected $name = 'Response';
    protected $description = 'Send output to browser';
    protected $hooks = array(
        'exit' => 'send',
        '404' => 'notFound',
        'empty' => 'showEmpty',
    );
    protected $is_core = TRUE;
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        cogear()->gears->Response OR $this->object(new Response_Object());
    }

    /**
     * Not found
     */
    public function notFound(){
        $this->request();
        $tpl = new Template('Response/templates/404');
        $tpl->show();
    }
    /**
     * Not found
     */
    public function showEmpty(){
        $this->request();
        warning(t('Nothing found','Response'),NULL,'content');
    }
}