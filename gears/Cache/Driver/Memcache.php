<?php

/**
 * Кеш через Memcache
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Driver_Memcache extends Cache_Driver_Abstract {

    protected $options = array(
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
        if ($memcache = self::check($this->options->host, $this->options->port)) {
            $this->object($memcache);
        } else {
            FALSE === $memcache && exit(t('Работа с кэшем через Memcache невозможна, ибо он отключен на сервере.'));
            if (NULL === $memcache) {
                throw new Exception(t('Не удаётся соединиться с сервером Memcache по адресу ') . $this->options->host . ':' . $this->options->port);
            }
        }
    }

    /**
     * Проверяет, работает ли Memcache на сервере
     *
     * @param   string  $host
     * @param   int     $port
     * @return  mixed   Memcache если работает, FALSE если не установлен, NULL если нет подключения
     */
    public static function check($host = '127.0.0.1', $port = '11211') {
        if (class_exists('Memcache')) {
            $memcache = new Memcache();
            $result = $memcache->connect($host, $port) ? $memcache : NULL;
            return $result;
        }
        return FALSE;
    }

    /**
     * Read from cache
     *
     * @param string $name
     * @return mixed|NULL
     */
    public function read($name) {
        if (FALSE === $this->options->enabled) {
            return FALSE;
        }
        $this->stats->read++;
        $name = $this->prepareKey($name);
        if (NULL !== ($data = $this->get($name))) {
            if (isset($data['tags']) && is_array($data['tags'])) {
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
        $this->stats->write++;
        $this->set($name, $data);
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
        $name = md5(SITE_URL . config('key')) . '_' . $this->options->prefix . '_' . $name;
        return $name;
    }

}