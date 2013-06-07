<?php

/**
 * Шестеренка для работы с программным кодом
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Code_Gear extends Gear {

    protected $hooks = array(
        'jevix' => 'hookJevix',
        'post.edit' => 'hookPostEdit',
        'parse' => 'hookParse'
    );

    /**
     * Парсинг кода
     */
    public function hookParse($item) {
        if($item->body){
            $item->body = preg_replace('#\<code\>([\n\r\s\t]+)#im','<code>',$item->body);
            $item->body = preg_replace('#\<\/pre\>([\n\r\s\t]+)#im','</pre>',$item->body);
        }
    }


    /**
     * Хук парсера Jevix
     *
     * Добавляем автоматом тег, чтобы наш код подсвечивался
     *
     * @param object $Jevix
     */
    public function hookJevix($Jevix) {
        $Jevix->cfgSetTagParamDefault('pre', 'class', 'prettyprint', true);
    }

    /**
     * Для того, чтобы prettyprint не лез в редактор, нужно удалить класс у <pre>
     *
     * @param object $Post
     * @param object $Form
     */
    public function hookPostEdit($Post, $Form) {
        $Post->body = str_replace('prettyprint', '', $Post->body);
    }

}