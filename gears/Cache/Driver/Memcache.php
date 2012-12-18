<?php

/**
 * Кеш через Memcached
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Driver_Memcache extends Cache_Driver_Abstract {

    public $options = array(
        'host' => '127.0.0.1',
        'port' => '11211',
        'prefix' => 'memcache',
    );

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        if (!self::check()) {
            throw new Exception(t('Работа с кэшем через Memcached невозможна, ибо он отключен на сервере.'));
        } else {
            $this->object(new Memcache());
            if (FALSE == $this->connect($this->options->host, $this->options->port)) {
                throw new Exception(t('Не удаётся соединиться с сервером Memcached по адресу %s:%d', $this->options->host, $this->options->port));
            }
        }
    }

    /**
     * Проверяет, работает ли мемкэша на сервере
     */
    public static function check() {
        return class_exists('Memcache');
    }

    /**
     * Read from cache
     *
     * @param string $name
     * @return mixed|NULL
     */
    public function read($name) {
        $name = $this->prepareKey($name);
        if (NULL !== ($data = $this->get($name))) {
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
        $this->set($name,$data);
    }

    /**
     * Remove cached element
     *
     * @param string $name
     */
    public function remove($name) {
        $this->delete($this->prepareKey($name));
    }

    /**
     * Clear cache folder
     */
    public function clear() {
        $this->flush();
    }

    /**
     * Подготавливает ключ для записи.
     *
     * Обратите внимание, что на одном Memcached-сервере могут работать разные сайты.
     *
     * @param string $name
     * @return string
     */
    protected function prepareKey($name) {
        $name = md5(cogear()->secure->genHash(config('site.url'))).'_'.$this->options->prefix.'_'.$name;
        return $name;
    }

}