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
        $this->uri = $this->sanitizePath(cogear()->request->get('PATH_INFO'));
        $this->segments = $this->parseSegments($this->uri);
        $this->routes = new Core_ArrayObject($this->routes);
        hook('ignite',array($this,'run'));
    }

    /**
     * Sanitize path
     *
     * @param	string	$path
     */
    public function sanitizePath($path) {
        $cogear = getInstance();
        // Sanitize unwanted data from the path
        $path = urldecode($path);
        $path = preg_replace('#[^' . config('permitted_uri_chars','\w-_.') . self::DELIM . ']+#imsu', '', $path);
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
    public function addRoute($route, $callback, $rewrite = FALSE) {
        if($rewrite OR !isset($this->routes[$route])){
            $this->routes[$route] = $callback;
        }
    }
    /**
     * Get uri
     * 
     * @return string
     */
    public function getUri(){
        return $this->uri;
    }
    /**
     * Get arguments
     * 
     * @return array
     */
    public function getArgs(){
        return $this->args;
    }
    /**
     * Get route matches
     * 
     * @return array
     */
    public function getMatches(){
        return $this->matches;
    }
    /**
     * Get segments
     *
     * @param   int     $num
     * @return array|string
     */
    public function getSegments($num = NULL){
        return $num ? (isset($this->segments[$num]) ? $this->segments[$num] : NULL) : $this->segments;
    }
    /**
     * Check uri match
     * 
     * @param string $uri
     * @param int $type 
     * @return  boolean
     */
    public function check($uri,$type = self::STARTS){
        switch($type){
            case self::STARTS:
                if(strpos($this->uri,$uri) === 0){
                    return TRUE;
                }
                break;
            case self::ENDS:
                if(strpos($this->uri,$uri) == (strlen($this->uri)-strlen($uri))){
                    return TRUE;
                }
                break;
            case self::BOTH:
                if(strpos($this->uri,$uri) !== FALSE){
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
        foreach ($this->routes as $route => $callback) {
            $route = str_replace(
                            $this->rules['from'],
                            $this->rules['to'],
                            $route);
            if (strpos($route, '^') === FALSE) {
                $route = '^' . $route;
            }
            if (strpos($route, '$') === FALSE) {
                $route .= '$';
            }
            $regexp = '#' . $route . '#isU';
            if (preg_match($regexp, $this->uri,$this->matches)) {
                $args = array();
                $root = trim(substr($route,0,strpos($route,'(')),self::DELIM);
                $exclude = strpos($root,self::DELIM) ? preg_split(self::DELIM, $root, -1, PREG_SPLIT_NO_EMPTY) : (array) $root;
                $this->args = array_merge($args,array_diff_assoc($this->segments,$exclude));
                // We have a nice method in hooks to prepare callback
                if($callback = Callback::prepare($callback)){
                    $this->callback = new Callback($callback);
                    event('callback.before',$this);
                    method_exists($callback[0],'request') && $callback[0]->request();
                    $this->callback->setArgs($this->args);
                    $this->callback->run();
                    event('callback.after',$this);
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
    public function exec($callback,$args = array()){
        if($callback = Callback::prepare($callback)){
            $callback = new Callback($callback);
            $args && $callback->setArgs($args);
            return $callback->run();
        }
        return NULL;
    }

}

/**
 * Routes
 * 
 * @param type $route
 * @param type $callback
 */
function route($route,$callback){
    cogear()->router->addRoute($route,$callback);
}