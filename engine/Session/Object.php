<?php

/**
 * Session handler
 *
 *
 *
 * @author      Dmitriy Belyaev <admin@cogear.ru>
 * @copyright   Copyright (c) 2010, Dmitriy Belyaev
 * @license     http://cogear.ru/license.html
 * @link        http://cogear.ru
 * @package     Core
 * @subpackage
 * @version     $Id$
 */
class Session_Object extends Cache_Object {

    protected $name;

    /**
     * Time to life
     *
     * @int
     */
    private $session_id_ttl = 3600;

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
	private $options;
    const HISTORY_STEPS = 10;

    /**
     * Constructor
     *
     * @param	array	$options
     */
    public function __construct($name) {
        hook('exit', array($this, 'close'));
        $this->name = $name;
        $this->options = clone config('session');
        $this->options->name = $this->name;
        $this->options->save_path = $this->options->path;
        $this->options->cookie_domain = '.' . SITE_URL;
        foreach (self::$iniOptions as $key => $option) {
            if ($this->options->$key) {
                if ($value = $this->options[$key] ? $this->options[$key] : $option) {
                    ini_set('session.' . $key, $value);
			    }
                $option && ini_set('session.' . $key, $option);
            } else {
            }
        }
		parent::__construct($this->options);
        $this->setHandler();
        $this->run();
    }
    /**
     * Set handler
     */
    private function setHandler() {
        session_set_save_handler(
                array($this->adapter, 'open'), array($this->adapter, 'close'), array($this->adapter, 'read'), array($this->adapter, 'write'), array($this->adapter, 'destroy'), array($this->adapter, 'gc')
        );
    }

    /**
     * Starts up the session system for current request
     */
    private function run() {
        session_name($this->name);
		if(empty($_SESSION)){
			session_start();
		}
        $session_id_ttl = $this->options['session_expire'];
        $this->init();

        // check if session id needs regeneration
        if ($this->sessionIdExpired()) {
            // regenerate session id (session data stays the
            // same, but old session storage is destroyed)
            $this->regenerateId();
        }
    }

    /**
     * Init
     */
    private function init() {
        $cogear = getInstance();
        event('session.init', $this);
        isset($_SESSION['user_agent']) OR $_SESSION['user_agent'] = $cogear->request->getUserAgent();
        $_SESSION['ip'] = $cogear->request->get('ip');
        $_SESSION['session_id'] = session_id();
        if (!isset($_SESSION['history'])) {
            $_SESSION['history'] = new Core_ArrayObject();
        } else {
            $last = end($_SESSION['history']);
        }
        $referer = $cogear->request->get('HTTP_REFERER', '/');
        if (!isset($last) OR $last != $referer) {
            $_SESSION['history'][] = $referer;
        }
        sizeof($_SESSION['history']) > self::HISTORY_STEPS && $_SESSION['history'] = new Core_ArrayObject(array_slice($_SESSION['history']->toArray(), sizeof($_SESSION['history']) - self::HISTORY_STEPS));
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
     * Regenerates session id
     */
    private function regenerateId() {
        // copy old session data, including its id
        $old_session_id = session_id();
        $old_session_data = $_SESSION;
        // regenerate session id and store it
        session_regenerate_id();
        $new_session_id = session_id();

        // switch to the old session and destroy its storage
        session_id($old_session_id);
        session_destroy();

        $this->setHandler();
        session_name($this->name);
        // switch back to the new session id and send the cookie
        session_id($new_session_id);
        session_start();

        // restore the old session data into the new session
        $_SESSION = $old_session_data;

        // update the session creation time
        $_SESSION['regenerated'] = time();

        session_write_close();
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
            case 'session_id':
                return session_id();
                break;
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
     * Close session
     */
    public function close() {
        session_write_close();
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
     * Checks if session has expired
     */
    function sessionIdExpired() {
        if (!isset($_SESSION['regenerated'])) {
            $_SESSION['regenerated'] = time();
            return FALSE;
        }

        $expiry_time = time() - $this->session_id_ttl;

        if ($_SESSION['regenerated'] <= $expiry_time) {
            return TRUE;
        }

        return FALSE;
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
        return call_user_func_array(array($this, 'unset'), $args);
    }

}
