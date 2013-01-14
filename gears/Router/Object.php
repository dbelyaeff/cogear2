<?php

/**
 * Router
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Router_Object extends Options implements Interface_Singleton {

    /**
     * Сущность
     *
     * @var object
     */
    private static $_instance;

    /**
     * Пути
     *
     * @var array
     */
    private $routes = array(
    );

    /**
     * Uri — тот фрагмент адреса, что идёт после домена
     *
     * @var string
     */
    protected $uri;

    /**
     * Сегменты Uri
     *
     * @var array
     */
    private $segments = array();

    /**
     * Аргументы
     *
     * @var array
     */
    private $args = array();

    /**
     * Совпадения
     *
     * @var array
     */
    protected $matches = array();

    /**
     * Обратный вызов
     *
     * @var array
     */
    protected $callback = array();

    /**
     * Шаблоны путей
     *
     * @array
     */
    private $rules = array(
        'from' => array(
            ':any',
            ':maybe',
            ':digit',
            ':alpha',
            ':word',
            ':index',
        ),
        'to' => array(
            '(.+?)',
            '(.*?)',
            '([\d]+)',
            '([a-z]+)',
            '([\w-_]+)',
            '\s*',
        )
    );

    /**
     * Разделитель uri
     *
     * @const
     */

    const DELIM = '/';
    const STARTS = 0;
    const ENDS = 1;
    const BOTH = 2;

    /**
     * Клонирование
     */
    private function __clone() {

    }

    /**
     * Сущность
     *
     * @return Cogear
     */
    public static function getInstance() {
        return self::$_instance instanceof self ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Конструктор
     */
    public function __construct() {
        $this->uri = $this->sanitizePath(server('uri'));
        $this->segments = $this->parseSegments($this->uri);
        $this->routes = new Core_ArrayObject($this->routes);
        $this->bind(':index', array(config('router.defaults.gear', 'Post'), config('router.defaults.action', 'index_action')));
        hook('ignite', array($this, 'run'));
    }

    /**
     * Очистка пути
     *
     * @param	string	$path
     */
    public function sanitizePath($path) {
        if (strpos($path, '?') !== FALSE) {
            $path = substr($path, 0, strpos($path, '?'));
        }
        // Sanitize unwanted data from the path
        $path = urldecode($path);
        $path = preg_replace('#[^' . config('permitted_uri_chars', '\w-_.') . self::DELIM . ']+#imsu', '', $path);
        return trim($path, '/');
    }

    /**
     * Обработка сегментов пути
     *
     * @param	string	$path
     * @return	string
     */
    private function parseSegments($path) {
        $cogear = getInstance();
        // Explode uri into pieces, but previosely trim it from aside delimiters and whitespaces
        return $this->segments = preg_split('#[' . preg_quote(self::DELIM) . ']+#', $path, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Привязывание пути
     *
     * @param string $route
     * @param callback $callback
     */
    public function bind($route, $callback, $prepend = FALSE) {
        if (!$this->routes->$route) {
            if ($prepend) {
                $this->routes->prepend($callback, $route);
            } else {
                $this->routes[$route] = $callback;
            }
        }
    }

    /**
     * Получение Uri
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Получение аргументов
     *
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

    /**
     * Получение совпадений роутера
     *
     * @return array
     */
    public function getMatches() {
        return $this->matches;
    }

    /**
     * Получение сегментов uri
     *
     * @param   int     $num
     * @return array|string
     */
    public function getSegments($num = NULL) {
        return $num !== NULL && isset($this->segments[$num]) ? $this->segments[$num] : $this->segments;
    }

    /**
     * Проверка пути
     *
     * @param string $route
     * @param string $uri
     * @param int $type
     * @return  boolean
     */
    public function check($route = '', $uri = NULL) {
        $site = 'http://' . SITE_URL . '/';
        if (strpos($route, $site) !== FALSE) {
            $route = str_replace($site, '', $route);
        }
        if (defined('FOLDER')) {
            $route = str_replace(FOLDER . '/', '', $route);
        }
        if (!$route) {
            return $this->uri ? FALSE : TRUE;
        }
        $regexp = $route;
        return (bool)preg_match('#'.$regexp.'#', $uri ? $uri : $this->uri);
    }

    /**
     * Обработка запроса
     */
    public function run() {
        if (!event('router.run', $this)->check()) {
            return;
        }
        foreach ($this->routes as $route => $callback) {
            $route = str_replace(
                    $this->rules['from'], $this->rules['to'], $route);
            $clean_route = $route;
            if (strpos($route, '^') === FALSE) {
                $route = '^' . $route;
            }
            if (strpos($route, '$') === FALSE) {
                $route .= '$';
            }
            $regexp = '|' . $route . '|isU';
            if (preg_match($regexp, $this->uri, $this->matches)) {
                $this->args = $this->prepareArgs(array_slice($this->matches, 1));
                $callback = new Callback($callback,$this->args);
                if ($this->exec($callback)) {
                    return;
                }
                return event('404');
            }
        }
        return event('404');
    }
    /**
     * Очищает аргументы от шелухи
     *
     * @param array $args
     * @return array
     */
    private function prepareArgs($args){
        foreach($args as &$arg){
            $arg = trim($arg,'/');
        }
        return $args;
    }
    /**
     * Выполнение обратного вызова
     */
    public function exec(Callback $callback) {
        if(!$callback->check()){
            return event('404');
        }
        if (!event('router.exec', $this, $callback)->check()) {
            return FALSE;
        }
        $gear = $callback->getCallback(0);
        $gear_name = $gear->gear;
        $method = $callback->getCallback(1);
        event('callback.before', $this);
        $event = $gear_name. '.' . $method;
        hook($event,array($gear,'request'));
        event($event, $gear, $method,$callback->getArgs());
        $callback->run();
        event('callback.after', $this);
        return TRUE;
    }

}

/**
 * Привязка пути
 *
 * @param type $route
 * @param type $callback
 */
function bind_route($route, $callback, $prepend = FALSE) {
    cogear()->router->bind($route, $callback, $prepend);
}

/**
 * Проверка пути
 *
 * @param type $route
 * @param type $arg
 * @return type
 */
function check_route($route = '', $uri = NULL) {
    return cogear()->router->check($route, $uri);
}