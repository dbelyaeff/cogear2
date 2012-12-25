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
    public function loadDir($dir, $ext = 'css') {
        parent::loadDir($dir, $ext);
    }

    /**
     * Заменяет относительные адреса на абсолютные
     *
     * @param type $file
     * @return type
     */
    public function parse($file) {
        $content = parent::parse($file);
        $style_dir = File::pathToUri(dirname($file));
        $content = preg_replace('#(url\([\'|\"]?)(\.?/)?#', '$1$2' . $style_dir . '/', $content);
        $content = preg_replace('#(url\([\'|\"]?)\.\./#', '$1' . dirname($style_dir) . '/', $content);
        return $content;
    }

    /**
     * Вывод скриптов
     */
    public function output() {
        if ($this->glue) {
            echo HTML::style(File::pathToUri($this->glue()));
        } else {
            foreach ($this as $style) {
                echo HTML::style(File::pathToUri($style)) . "\n";
            }
        }
    }

}