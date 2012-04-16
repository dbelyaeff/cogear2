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

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->adapter = new Response_Object();
    }
    
    /**
     * Send output to user
     */
    public function send(){
        $this->adapter->send();
    }
    
    /**
     * Not found
     */
    public function notFound(){
        $this->request();
        $tpl = new Template('Response.404');
        $tpl->show();
    }
    /**
     * Not found
     */
    public function showEmpty(){
        $this->request();
        warning(t('Nothing found','Response'));
    }
}