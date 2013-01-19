<?php

/**
 * Кеш через APC
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Driver_APC extends Cache_Driver_Abstract {

    protected $options = array(
        'prefix' => 'APC',
    );

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        if (!self::check()) {
            exit(t('Работа с кэшем через APC невозможна, ибо он отключен на сервере.'));
        }
    }

    /**
     * Проверяет, работает ли APC на сервере
     *
     * @param   string  $host
     * @param   int     $port
     * @return  mixed   APC если работает, FALSE если не установлен
     */
    public static function check() {
        return function_exists('apc_fetch');
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
        if (NULL !== ($data = apc_fetch($name))) {
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
        );
        if ($tags) {
            $data['tags'] = $tags;
            foreach ($tags as $tag) {
                $this->write('tags/' . $tag, '', array());
            }
        }
        $this->stats->write++;
        apc_store($name, $data, $ttl ? time() + $ttl : 0);
    }

    /**
     * Remove cached element
     *
     * @param string $name
     */
    public function remove($name) {
        apc_delete($this->prepareKey($name));
    }

    /**
     * Clear cache folder
     */
    public function clear() {
        apc_clear_cache();
    }

    /**
     * Подготавливает ключ для записи.
     *
     * Обратите внимание, что на одном APC'е могут работать разные сайты.
     *
     * @param string $name
     * @return string
     */
    protected function prepareKey($name) {
        $name = md5(SITE_URL . config('key')) . '_' . $this->options->prefix . '_' . $name;
        return $name;
    }

}