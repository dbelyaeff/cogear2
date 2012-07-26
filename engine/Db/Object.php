<?php

/**
 * Database object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Db_Object extends Object {

    public $options = array(
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'prefix' => '',
        'database' => 'cogear',
        'adapter' => '',
    );

    /**
     * Constructor
     */
    public function __construct($options) {
        parent::__construct();
        if (is_string($options)) {
            if (filter_var($options, FILTER_VALIDATE_URL)) {
                if ($options = self::parseDSN($options)) {
                    $this->options->extend($options);
                }
            } else {
                error(t('Provided database DSN string is not correct!', 'Db.errors'));
            }
        } else {
            $this->options->extend($options);
        }
        $this->attach(new $this->options->adapter($this->options));
    }

    /**
     * Check data source name
     *
     * @param string $dsn
     * @return boolean
     */
    public static function parseDSN($dsn) {
        $config = parse_url($dsn);
        if (isset($config['query'])) {
            parse_str($config['query'], $query);
            $config = array_merge($config, $query);
        }
        if (!isset($config['host']))
            $config['host'] = 'localhost';
        if (!isset($config['user']))
            $config['user'] = 'root';
        if (!isset($config['pass']))
            $config['pass'] = '';
        if (!isset($config['prefix']))
            $config['prefix'] = config('database.prefix', '');
        $config['database'] = trim($config['path'], ' /');
        $config['adapter'] = ucfirst($config['scheme']);
        if (!class_exists($config['adapter'])) {
            $config['adapter'] = 'Db_Driver_' . ucfirst($config['scheme']);
            if (!class_exists($config['adapter'])) {
                error(t('Database driver <b>%s</b> not found.', 'Database errors', ucfirst($config['scheme'])));
                return FALSE;
            }
        }
        return $config;
    }
}