<?php

/**
 * Abstract i18n adapter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         I18n

 */
abstract class I18n_Driver_Abstract extends Options {

    protected $options = array(
        'lang' => 'en',
        'path' => LANG,
        'file' => '',
    );

    const SECTION_PREFIX = '#';

    /**
     * Get text
     *
     * @param   string  $text
     * @param   string  $section
     */
    public function get($text) {
        if ($this->$text) {
            return $this->$text;
        }
        return $text;
    }

    /**
     * Get text
     *
     * @param   string  $text
     * @param   string  $value
     * @return  this
     */
    public function set($key, $value) {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Import text
     *
     * @param   string  $text
     * @param   string  $section
     * @return  this
     */
    public function import($data, $rewrite = FALSE) {
        foreach ($data as $key => $value) {
            if (!$rewrite && $this->$key)
                continue;;
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Export text
     *
     * @param   string  $text
     * @param   string  $section
     */
    public function export() {
        return $this->toArray();
    }

    /**
     * Формирование файла для записи
     *
     * @return type
     */
    public function getPath() {
        return $this->options->file ? $this->options->file : $this->options->path . DS . $this->options->lang . EXT;
    }

    /**
     * Load data
     */
    abstract public function load($path = NULL);

    /**
     * Save data
     */
    abstract public function save($path = NULL);

    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (!$this->offsetExists($name)) {
            return NULL;
        }
        return $this->offsetGet($name);
    }

}