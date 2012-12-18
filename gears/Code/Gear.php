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
    );

    /**
     * Хук парсера Jevix
     *
     * Добавляем автоматом тег, чтобы наш код подсвечивался
     *
     * @param object $Jevix
     */
    public function hookJevix($Jevix){
         $Jevix->cfgSetTagParamDefault('pre', 'class', 'prettyprint', true);
    }
    /**
     * Переопределяем загрузку скриптов и стилей
     */
 /*   public function loadAssets() {
        //parent::loadAssets();

        $this->assets->addStyle($this->dir . DS . 'css' . DS . 'prettify.css');
        $this->assets->addScript($this->dir . DS . 'js' . DS . 'prettify.js');
        $this->assets->addScript($this->dir . DS . 'js' . DS . 'script.js');
    }
*/

}