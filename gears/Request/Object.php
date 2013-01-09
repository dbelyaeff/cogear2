<?php

/**
 * Объект Запроса
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Request_Object {

    /**
     * Информация о сервере
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
     * Метод запроса
     *
     * @var  string
     */
    private $method = 'GET';
    /**
     * Протокол запроса
     *
     * @var  string
     */
    private $protocol = 'http';
    /**
     * Хост из конфига сайта
     *
     * @var type
     */
    private $host;
    /**
     * Реферер
     *
     * @var  string
     */
    private $referrer = '';
    /**
     * Если запрос аяксовый
     *
     * @var boolean
     */
    private $is_ajax = FALSE;

    /**
     * Конструктор
     */
    public function __construct() {
        // Вызываем статический класс, чтобы дальше работал его метод-ссылка l();
        $this->host = Url::link();
        $string_filter = array(FILTER_SANITIZE_SPECIAL_CHARS, FILTER_SANITIZE_STRING);
        $path_filter = array(FILTER_SANITIZE_URL, FILTER_FLAG_PATH_REQUIRED);
        $this->server = new Core_ArrayObject($_SERVER);
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
            $this->referer = $this->server['HTTP_REFERER'];
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
        switch($name){
            case 'uri':
                if($result = $this->get('PATH_INFO')){
                    return $result;
                }
                elseif($result = $this->get('REQUEST_URI')){
                    return $result;
                }
                elseif($result = $this->get('ORIG_PATH_INFO')){
                    return $result;
                }
                elseif($result = $this->get('QUERY_STRING')){
                    return $result;
                }
                break;
            default:
            return isset($this->$name) ? $this->$name : (isset($this->server[$name]) ? $this->server[$name] : ($default ? $default : NULL));
        }
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
/**
 * Request param get alias
 *
 * @param type $param
 * @param type $default
 * @return type
 */
function server($param,$default = NULL){
    if($param = cogear()->request->get($param)){
        return $param;
    }
    else {
        return $default;
    }
}