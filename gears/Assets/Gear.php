<?php

/**
 * Шестеренка для управления подключаемыми JS и CSS файлами
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Assets_Gear extends Gear {

    public $js;
    public $css;
    /**
     * Вывод глобальный переменных в заголовке
     */
    public function hookHead() {
        $cogear = new Core_ArrayObject();
        $cogear->settings = new Core_ArrayObject();
        $cogear->settings->site = config('site.url');
        event('assets.js.global', $cogear);
        echo HTML::script("var cogear = cogear || " . json_encode($cogear),array(), TRUE);
    }

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        // Важно повесить хук именно таким образом, чтобы информация выводилась до остальных скриптов
        hook('head',array($this,'hookHead'));
        $this->js = Assets::factory('js', config('assets.js'));
        $this->css = Assets::factory('css', config('assets.css'));
    }

}

/**
 * Функции-ярлыки
 */
function css($url, $region = 'content') {
    append($region, HTML::style($url));
}

function js($url, $region = 'content') {
    append($region, HTML::script($url));
}

function inline_js($code, $region = 'content') {
    append($region, HTML::script($code, array(), TRUE));
}