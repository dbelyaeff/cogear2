<?php

/**
 * Request
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Request {

    /**
     * Server info
     * 
     * @var array
     */
    protected $server = array();
    protected $ip = '';
    protected $user_agents = array(
        'firefox' => 'firefox/(?<version>.*+)',
        'ie' => 'msie\s(?<version>[\d.]+)',
        'opera' => 'opera.*version/(?<version>[\d.]+)',
        'chrome' => 'chrome/(?<version>[\d.]+)',
        'safari' => 'safari/(?<version>[\d.]+).*',
        'mobilesafari' => 'version/(?<version>[\d.]+).*safari',
    );

    /**
     * Request method
     *
     * @var  string
     */
    private $method = 'GET';
    /**
     * Request protocol
     *
     * @var  string
     */
    private $protocol = 'http';
    /**
     * Referer
     *
     * @var  string
     */
    private $referrer = '';
    /**
     * If request is ajaxed
     * 
     * @var boolean
     */
    private $is_ajax = FALSE;

    /**
     * Constructor
     */
    public function __construct() {
        $string_filter = array(FILTER_SANITIZE_SPECIAL_CHARS, FILTER_SANITIZE_STRING);
        $path_filter = array(FILTER_SANITIZE_URL, FILTER_FLAG_PATH_REQUIRED);
        $this->server = filter_input_array(INPUT_SERVER, array(
                    'HTTP_HOST' => FILTER_SANITIZE_URL,
                    'HTTP_USER_AGENT' => $string_filter,
                    'HTTP_ACCEPT' => $string_filter,
                    'HTTP_ACCEPT_LANGUAGE' => $string_filter,
                    'HTTP_ACCEPT_CHARSET' => $string_filter,
                    'HTTP_ACCEPT_ENCODING' => $string_filter,
                    'SERVER_SIGNATURE' => $string_filter,
                    'SERVER_SOFTWARE' => $string_filter,
                    'SERVER_NAME' => $string_filter,
                    'SERVER_ADDR' => FILTER_VALIDATE_IP,
                    'SERVER_PORT' => FILTER_SANITIZE_NUMBER_INT,
                    'REMOTE_ADDR' => FILTER_VALIDATE_IP,
                    'HTTP_CLIENT_IP' => FILTER_VALIDATE_IP,
                    'HTTP_X_FORWARDED_FOR' => FILTER_VALIDATE_IP,
                    'SERVER_PROTOCOL' => $string_filter,
                    'DOCUMENT_ROOT' => $path_filter,
                    'SERVER_ADMIN' => FILTER_SANITIZE_EMAIL,
                    'SCRIPT_FILENAME' => $path_filter,
                    'PATH_INFO' => $string_filter,
                    'QUERY_STRING' => $string_filter,
                    'REQUEST_URI' => $string_filter,
                    'SCRIPT_NAME' => $path_filter,
                    'PHP_SELF' => $path_filter,
                    'HTTP_REFERER' => FILTER_VALIDATE_URL,
                    'REQUEST_METHOD' => FILTER_SANITIZE_STRING,
                    'HTTPS' => FILTER_VALIDATE_BOOLEAN,
                    'HTTP_X_REQUESTED_WITH' =>  FILTER_SANITIZE_STRING,
                ));
        foreach(array('HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR') as $ip){
            if(isset($this->server[$ip])){
                $this->ip = $this->server[$ip];
                continue;
            }
        }
        $this->method = $this->server['REQUEST_METHOD'];
        if(!empty($this->server['HTTPS'])){
            $this->protocol = 'https';
        }
        if(!empty($this->server['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($this->server['HTTP_X_REQUESTED_WITH'])){
            $this->is_ajax = TRUE;
        }
        if(!empty($this->server['HTTP_REFERER'])){
            $this->referer = 'REFERER';
        }
    }
    /**
     * Check if request is ajax
     * 
     * @return boolean
     */
    public function isAjax(){
        return $this->is_ajax;
    }
    /**
     * Get request info
     * 
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function get($name,$default = '') {
        return isset($this->$name) ? $this->$name : (isset($this->server[$name]) ? $this->server[$name] : ($default ? $default : NULL));
    }

    /**
     * Check user agent
     *
     * You can write Firefox, firefox or Firefox 0.9, for example.
     *
     * @param string $ua
     */
    public function checkUserAgent($ua) {
        return strstr(strtolower($this->user_agent['name']), strtolower($ua));
    }

    /**
     * Define user agent
     *
     * Adapted from http://php.net/manual/en/function.get-browser.php
     * @return array
     */
    public function getUserAgent() {
        $user_agent = strtolower($this->server['HTTP_USER_AGENT']);
        $browser = NULL;
        $version = NULL;
        $os = NULL;
        $is_mobile = NULL;
        foreach ($this->user_agents as $name => $pattern) {
            if (!$browser && preg_match('#' . $pattern . '#is', $user_agent, $matches)) {
                $browser = $name;
                $full_version = $matches['version'];
                $version = intval($full_version);
            }
        }
        if (preg_match('/mobile/', $user_agent)) {
            $is_mobile = TRUE;
        }
        if (preg_match('/win/', $user_agent)) {
            $os = 'windows';
        } elseif (preg_match('/linux/', $user_agent)) {
            $os = 'linux';
        } elseif (preg_match('/android/', $user_agent)) {
            $os = 'android';
        } elseif (preg_match('/iphone os/', $user_agent)) {
            $os = 'ios';
        } elseif (preg_match('/mac/', $user_agent)) {
            $os = 'mac';
        }

        if (preg_match('#(\s|;)(?<locale>\w{2}(-\w{2})?)(\)|;)#', $user_agent, $matches)) {
            $locale = $matches['locale'];
        }
        return array(
            'browser' => $browser,
            'version' => $version,
            'os' => $os,
            'is_mobile' => $is_mobile,
            'locale' => isset($locale) ? $locale : config('site.locale','en'),
        );
    }

}