<?php

/**
 * File cache
 *
 *
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Cache_Adapter_File extends Cache_Adapter_Abstract {

    /**
     * Flag indicates cache state
     *
     * @var boolean
     */
    protected $enabled = TRUE;

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        File::mkdir($this->options->path);
    }

    /**
     * Read from cache
     *
     * @param string $name
     * @param boolean $force
     * @return mixed|NULL
     */
    public function read($name, $force = FALSE) {
        $name = $this->prepareKey($name);
        $path = $this->options->path . DS . $name;
        if (file_exists($path)) {
            $data = Config::read($path, Config::AS_ARRAY);
            if ($force) {
                return $data['value'];
            }
            if ($data['ttl'] && time() > $data['ttl']) {
                return NULL;
            } elseif (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    if (NULL === $this->read('tags/' . $tag)) {
                        return NULL;
                    }
                }
            }
            return $data['value'];
        }
        return NULL;
    }

    /**
     * Write to cache
     *
     * @param string $name
     * @param mixed $value
     * @param array $tags
     * @param int $ttl
     */
    public function write($name, $value, $tags = NULL, $ttl = NULL) {
        $name = $this->prepareKey($name);
        $data = array(
            'value' => $value,
            'ttl' => $ttl ? time() + $ttl : 0,
        );
        if ($tags) {
            $data['tags'] = $tags;
            foreach ($tags as $tag) {
                $this->write('tags/' . $tag, '', array());
            }
        }
        File::mkdir(dirname($this->options->path . DS . $name));
        file_put_contents($this->options->path . DS . $name, PHP_FILE_PREFIX . 'return ' . var_export($data, TRUE) . ';');
    }

    /**
     * Remove cached element
     *
     * @param string $name
     */
    public function remove($name) {
        $file = $this->options->path . DS . $this->prepareKey($name);
        file_exists($file) && unlink($file);
    }

    /**
     * Clear cache folder
     */
    public function clear() {
        if ($result = glob($this->options->path . DS . '*' . EXT)) {
            foreach ($result as $path) {
                unlink($path);
            }
        }
    }

    /**
     *  Prepare filaname for cache
     * @param string $name
     * @return string
     */
    protected function prepareKey($name) {
        $name = str_replace('/', DS, $name . EXT);
        return $name;
    }

}