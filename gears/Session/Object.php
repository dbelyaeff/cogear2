<?php

/**
 * Session handler
 *
 *
 *
 * @author      Беляев Дмитрий <admin@cogear.ru>
 * @copyright   Copyright (c) 2010, Беляев Дмитрий
 * @license     http://cogear.ru/license.html
 * @link        http://cogear.ru
 * @package     Core
 * @subpackage
 * @version     $Id$
 */
class Session_Object extends Cache_Object {

    /**
     * Setting that can redifine php.ini optionsuration
     *
     * @array
     */
    private static $iniOptions = array(
        'save_path' => NULL,
        'name' => NULL,
        'save_handler' => NULL,
        'auto_start' => NULL,
        'gc_probability' => NULL,
        'gc_divisor' => NULL,
        'gc_maxlifetime' => NULL,
        'serialize_handler' => NULL,
        'cookie_lifetime' => 86400,
        'cookie_path' => '/',
        'cookie_domain' => NULL,
        'cookie_secure' => NULL,
        'cookie_httponly' => NULL,
        'use_cookies' => 'on',
        'use_only_cookies' => 'on',
        'referer_check' => NULL,
        'entropy_file' => NULL,
        'entropy_length' => NULL,
        'cache_limiter' => NULL,
        'cache_expire' => NULL,
        'use_trans_sid' => NULL,
        'bug_compat_42' => NULL,
        'bug_compat_warn' => NULL,
        'hash_function' => NULL,
        'hash_bits_per_character' => NULL
    );

    /**
     * Конструктор
     *
     * @param	array	$options
     */
    public function __construct($options) {
        $defaults = array(
            'adapter' => 'Session_Adapter_File',
            'save_path' => CACHE . DS . 'sessions',
            'path' => CACHE . DS . 'sessions',
            'cookie_domain' => '.' . config('site.url', cogear()->request->get('HTTP_HOST')),
            'session_expire' => 3600,
        );
        $options = array_merge($defaults, $options);
        parent::__construct($options);
        foreach (self::$iniOptions as $key => $option) {
            if ($this->options->$key) {
                if ($value = $this->options[$key] ? $this->options[$key] : $option) {
                    ini_set('session.' . $key, $value);
                }
                $option && ini_set('session.' . $key, $option);
            }
        }
        session_set_save_handler(
                array($this->object, 'open'), array($this->object, 'close'), array($this->object, 'read'), array($this->object, 'write'), array($this->object, 'destroy'), array($this->object, 'gc')
        );
        $this->init();
    }

    /**
     * Init
     */
    private function init() {
        session_id() OR session_start();
        $cogear = getInstance();
        event('session.init', $this);
        isset($_SESSION['user_agent']) OR $_SESSION['user_agent'] = $cogear->request->getUserAgent();
        $_SESSION['ip'] = $cogear->request->get('ip');
        $_SESSION['session_id'] = session_id();
        $referer = $cogear->request->get('HTTP_REFERER', '/');
    }

    /**
     * Browsing history — last 10 pages
     *
     * @param   int $page
     * @param   string  $default
     * @return  string|NULL
     */
    public function history($page = 0, $default = NULL) {
        $current = sizeof($_SESSION['history']);
        $needle = $current + $page;
        return isset($_SESSION['history'][$needle]) ? $_SESSION['history'][$needle] : ($default ? $default : NULL);
    }

    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * Magic __set method
     *
     * @param string $name
     * @return mixed
     */
    public function __set($name, $value) {
        return $this->set($name, $value);
    }

    /**
     * __isset magic method
     *
     * @param type $name
     */
    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

    /**
     * Reads given session attribute value
     * You can use default value and it will be returned and saved if there is no session variable before
     *
     * @param string  	$name
     * @param	mixed	$default Default value
     * @return mixed
     */
    public function get($name = NULL, $default = NULL) {
        if (!$name) {
            return $_SESSION;
        }
        switch ($name) {
            default:
                if (!isset($_SESSION[$name])) {
                    if ($default) {
                        return $default;
                    }
                    return NULL;
                }
                return $_SESSION[$name];
        }
        return NULL;
    }

    /**
     * Get flash variable.
     * Immedeately delete variable after get it.
     *
     * @param	string	$name
     * @param	mixed	$default
     */
    public function flash($name, $default=NULL) {
        $result = $this->get($name, $default);
        $this->destory($name);
        return $result;
    }

    /**
     * Sets session attributes to the given values
     *
     * @param string|array $name  Variable name or array of variables
     * @param mixed   $value
     */
    public function set($name = array(), $value = '') {
        if (is_string($name)) {
            $_SESSION[$name] = $value;
        } elseif (is_array($name) OR $name instanceof Traversable) {
            foreach ($name as $key => $val) {
                $this->set($key, $val);
            }
        }
    }

    /**
     * Remove session variable
     *
     * @param string  $name
     */
    public function destroy($name = array()) {
        if (!$name) {
            unset($_SESSION);
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 42000, '/');
            }
            session_destroy();
        } else {
            if (is_string($name)) {
                $name = array($name => '');
            }

            if (is_array($name)) {
                foreach ($name as $key => $val) {
                    unset($_SESSION[$key]);
                }
            }
        }
    }

    /**
     * Alias to unset method
     */
    public function remove() {
        $args = func_get_args();
        return call_user_func_array(array($this, 'destroy'), $args);
    }

    /**
     * Alias to unset method
     */
    public function delete() {
        $args = func_get_args();
        return call_user_func_array(array($this, 'destroy'), $args);
    }

}
