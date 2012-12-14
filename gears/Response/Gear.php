<?php

/**
 * Шестеренка Ответ
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Response_Gear extends Gear {

    protected $hooks = array(
        'exit' => 'send',
        '404' => 'notFound',
        'empty' => 'showEmpty',
    );
    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->object(new Response_Object());
//        hook('exit',array($this->object(),'send'));
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