<?php

/**
 * Драйвер для загрузки стилей
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Assets_Driver_CSS extends Assets_Driver_Abstract {

    /**
     * Загрузка директории во внутренне хранилище
     *
     * @param string $dir
     * @param string $ext
     */
    public function loadDir($dir,$ext = 'css') {
        parent::loadDir($dir, $ext);
    }
    /**
     * Вывод скриптов
     */
    public function output() {
        foreach($this as $style){
            echo HTML::style(File::pathToUri($style))."\n";
        }
    }

}