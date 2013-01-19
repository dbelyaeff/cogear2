<?php

/**
 * Шестеренка кеширования
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Gear extends Gear {

    protected $hooks = array(
        'dev.trace' => 'hookTrace',
        'response.send' => 'hookResponseSend',
    );

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        hook('preload', array($this, 'hookPreload'));
        $this->object(Cache::factory('normal', config('cache')));
    }

    /**
     * Вывод отладочной информации в подвал темы
     *
     * @param Stack $Stack
     */
    public function hookTrace() {
        echo template('Cache/templates/trace');
    }

    /**
     * Данный хук кэширует выдачу, если пользователь неавторизован на сайте
     *
     * @param Reponse_Object $response
     */
    public function hookResponseSend($response) {
        if (!config('cache.guests')) {
            return;
        }
        if (0 === user()->id && !cache('pagecache/' . $uri)) {
            $uri = $this->router->getUri();
            $uri OR $uri = 'index';
            cache('pagecache/' . $uri, $response->output, array(), config('cache.guests', 3600));
        }
    }

    /**
     * Если
     */
    public function hookPreload() {
        if (!config('cache.guests')) {
            return;
        }
        $uri = $this->router->getUri();
        $uri OR $uri = 'index';
        if (empty($_POST) && empty($_GET) && NULL == session('uid') && $response = cache('pagecache/' . $uri)) {
            bench('done');
            $bench = bench();
            $data = humanize_bench($bench['done']);
            exit($response . '<!-- ' . round($data['time'], 3) . ' ' . $data['memory'] . '-->');
        }
    }

}

/**
 * Caching alias
 *
 * @param type $name
 * @param type $value
 * @param type $tags
 * @param type $ttl
 * @return type
 */
function cache($name, $value = NULL, $tags = array(), $ttl = 3600) {
    if ($value !== NULL) {
        return cogear()->cache->write($name, $value, $tags, $ttl);
    } else {
        return cogear()->cache->read($name);
    }
}