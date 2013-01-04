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
     * Routes
     *
     * @var array
     */
    private $routes = array(
    );

    /**
     * URI — path or URL after domain address.
     *
     * @var string
     */
    protected $uri;

    /**
     * URI segments
     *
     * @var array
     */
    private $segments = array();

    /**
     * Arguments — filtered segments
     *
     * @var array
     */
    private $args = array();

    /**
     * Matches
     *
     * @var array
     */
    protected $matches = array();

    /**
     * Callback
     *
     * @var array
     */
    protected $callback = array();

    /**
     * Route expression transformation variables
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
     * Url delimiter
     *
     * @const
     */

    const DELIM = '/';
    const STARTS = 0;
    const ENDS = 1;
    const BOTH = 2;

    /**
     * Clone
     */
    private function __clone() {

    }

    /**
     * Get instance
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
     * Sanitize path
     *
     * @param	string	$path
     */
    public function sanitizePath($path) {
        $cogear = getInstance();
        if (strpos($path, '?') !== FALSE) {
            $path = substr($path, 0, strpos($path, '?'));
        }
        // Sanitize unwanted data from the path
        $path = urldecode($path);
        $path = preg_replace('#[^' . config('permitted_uri_chars', '\w-_.') . self::DELIM . ']+#imsu', '', $path);
        return trim($path, '/');
    }

    /**
     * Parse path segments
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
     * Add route
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
     * Get uri
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Get arguments
     *
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

    /**
     * Get route matches
     *
     * @return array
     */
    public function getMatches() {
        return $this->matches;
    }

    /**
     * Get segments
     *
     * @param   int     $num
     * @return array|string
     */
    public function getSegments($num = NULL) {
        return $num !== NULL && isset($this->segments[$num]) ? $this->segments[$num] : $this->segments;
    }

    /**
     * Check uri match
     *
     * @param string $uri
     * @param int $type
     * @return  boolean
     */
    public function check($uri = '', $type = self::STARTS) {
        $site = 'http://' . SITE_URL . '/';
        if (strpos($uri, $site) !== FALSE) {
            $uri = str_replace($site, '', $uri);
        }
        if (defined('FOLDER')) {
            $uri = str_replace(FOLDER . '/', '', $uri);
        }
        if (!$uri) {
            return $this->uri ? FALSE : TRUE;
        }
        $regexp = preg_quote($uri,'#');
        switch($type){
            case self::STARTS:
                $regexp = '^'.$regexp;
                break;
            case self::ENDS:
                $regexp .= '$';
                break;
            case self::BOTH:
                $regexp = '^'.$regexp.'$';
                break;
        }
        return preg_match('#'.$regexp.'#', $this->uri);
    }

    /**
     * Run dispatched request
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
            }
        }
        event('404');
        return;
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
     * Execute callback
     *
     */
    public function exec(Callback $callback) {
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
 * Routes
 *
 * @param type $route
 * @param type $callback
 */
function bind_route($route, $callback, $prepend = FALSE) {
    cogear()->router->bind($route, $callback, $prepend);
}

/**
 * Check route alias
 *
 * @param type $route
 * @param type $arg
 * @return type
 */
function check_route($route = '', $arg = Router::BOTH) {
    return cogear()->router->check($route, $arg);
}