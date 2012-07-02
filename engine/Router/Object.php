<?php

/**
 * Router
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Router_Object extends Options {

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
     * Constructor
     */
    public function __construct() {
        $this->uri = $this->sanitizePath(server('uri'));
        $this->segments = $this->parseSegments($this->uri);
        $this->routes = new Core_ArrayObject($this->routes);
        hook('ignite', array($this, 'run'));
    }

    /**
     * Sanitize path
     *
     * @param	string	$path
     */
    public function sanitizePath($path) {
        $cogear = getInstance();
        if(strpos($path,'?') !== FALSE){
            $path = substr($path,0,strpos($path,'?'));
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
                $this->routes->prepend($callback,$route);
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
    public function check($uri, $type = self::STARTS) {
        $site = 'http://' . SITE_URL . '/';
        if (strpos($uri, $site) !== FALSE) {
            $uri = str_replace($site, '', $uri);
        }
        if(defined('FOLDER')){
            $uri = str_replace(FOLDER.'/','',$uri);
        }
        if (!$uri) {
            return $this->uri ? FALSE : TRUE;
        }
        switch ($type) {
            case self::STARTS:
                if (strpos($this->uri, $uri) === 0) {
                    return TRUE;
                }
                break;
            case self::ENDS:
                if (strpos($this->uri, $uri) == (strlen($this->uri) - strlen($uri))) {
                    return TRUE;
                }
                break;
            case self::BOTH:
                if (strpos($this->uri, $uri) !== FALSE && strlen($this->uri) == strlen($uri)) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Run dispatched request
     */
    public function run() {
        $cogear = getInstance();
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
            $regexp = '#' . $route . '#isU';
            if (preg_match($regexp, $this->uri, $this->matches)) {
                $args = array();
                $root = trim(substr($clean_route, 0, strpos($clean_route, '(')), self::DELIM);
                $exclude = strpos($root, self::DELIM) ? preg_split(self::DELIM, $root, -1, PREG_SPLIT_NO_EMPTY) : (array) $root;
                $this->args = array_merge($args, array_diff_assoc($this->segments, $exclude));
                // We have a nice method in hooks to prepare callback
                if ($this->exec($callback, $this->args)) {
                    return;
                }
            }
        }
        event('404');
        return;
    }

    /**
     * Execute callback
     *
     */
    public function exec($callback, $args = array()) {
        if ($callback = Callback::prepare($callback)) {
            if (!event('router.exec', $callback)->check()) {
                return;
            }
            $this->callback = new Callback($callback);
            event('callback.before', $this);
            method_exists($callback[0], 'request') && $callback[0]->request();
            $this->callback->setArgs($args);
            $this->callback->run();
            event('callback.after', $this);
            return TRUE;
        }
        return FALSE;
    }

}

/**
 * Routes
 *
 * @param type $route
 * @param type $callback
 */
function route($route, $callback, $prepend = FALSE) {
    cogear()->router->bind($route, $callback, $prepend);
}

/**
 * Check route alias
 *
 * @param type $route
 * @param type $arg
 * @return type
 */
function check_route($route, $arg = Router::BOTH) {
    return cogear()->router->check($route, $arg);
}