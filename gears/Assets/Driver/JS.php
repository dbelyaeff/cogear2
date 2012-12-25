<?php

/**
 * Абстрактный класс драйвера активов
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Assets_Driver_JS extends Assets_Driver_Abstract {

    /**
     * Вывод скриптов
     */
    public function output() {
        if ($this->glue) {
            echo HTML::script(File::pathToUri($this->glue()));
        } else {
            foreach ($this as $script) {
                echo HTML::script(File::pathToUri($script)) . "\n";
            }
        }
    }

}