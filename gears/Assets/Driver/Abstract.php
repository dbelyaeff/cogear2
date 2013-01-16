<?php

/**
 * Драйвер для загрузки скриптов
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
abstract class Assets_Driver_Abstract extends Object {

    protected $options = array(
        'glue' => TRUE,
        'refresh' => 0,
    );
    protected $is_rendered;
    protected $is_glued;

    /**
     * Конструкторв
     *
     * @param type $options
     */
    public function __construct($options = NULL) {
        parent::__construct($options);
    }

    /**
     * Загрузка директории во внутренне хранилище
     *
     * @param string $dir
     * @param string $ext
     */
    public function loadDir($dir, $ext = 'js') {
        if (isset($dir) && $files = glob($dir . DS . '*.' . $ext)) {
            foreach ($files as $file) {
                $this->findByValue($file) OR $this->append($file);
            }
        }
    }

    /**
     * Склеивает содержимое файлов
     *
     * @return  string  адрес файла
     */
    protected function glue() {
        $filename = $this->options->filename OR $this->genFilename();
        $dir = CACHE . DS . 'assets';
        is_dir($dir) OR File::mkdir($dir);
        $file = $dir . DS . $filename;
        // Если файл не существует или пришло время обновления
        if (!$this->is_glued && (!file_exists($file) OR (time() - filemtime($file) > $this->options->refresh))) {
            $output = '';
            // Парсим все файлы
            foreach ($this as $path) {
                $output .= $this->parse($path) . "\n";
            }
            file_put_contents($file, $output);
            // Ставим флаг
            $this->is_glued = TRUE;
        }
        return $file;
    }

    /**
     * Парсер файла
     *
     * @param string $file
     * @return  string
     */
    public function parse($file) {
        return file_get_contents($file);
    }

    /**
     * Генерирует имя файла
     *
     * @return  string
     */
    public function genFilename() {
        $first_file = $this->offsetGet(0);
        $ext = pathinfo($first_file, PATHINFO_EXTENSION);
        $name = $this->options->name;
        return $name . '.' . $ext;
    }

    abstract function output();
}