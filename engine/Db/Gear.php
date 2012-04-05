<?php

/**
 * Database
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Db_Gear extends Gear {

    protected $name = 'Database';
    protected $description = 'Database operations management';
    protected $order = -1000;
    protected $type = Gear::MODULE;
    protected $driver;
    public static $error_codes = array(
        100 => 'Driver not found.',
        101 => 'Couldn\'t connect to the database.',
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
        if ($dsn = $this->get('database.dsn')) {
            if (!$this->checkDSN($dsn)) {
                error(t('Couldn\'t establish database connection.', 'Db.errors'));
            } else {
                hook('done', array($this, 'showErrors'));
                hook('debug', array($this, 'trace'));
                cogear()->db = $this->driver;
            }
        } else {
            error(t('Database connection string is not defined.', 'Db.errors'));
        }
    }

    /**
     * Check data source name
     *  
     * @param string $dsn
     * @return boolean
     */
    public function checkDSN($dsn) {
        $config = parse_url($dsn);
        if (isset($config['query'])) {
            parse_str($config['query'], $query);
            $config += $query;
        }
        if (!isset($config['host']))
            $config['host'] = 'localhost';
        if (!isset($config['user']))
            $config['user'] = 'root';
        if (!isset($config['pass']))
            $config['pass'] = '';
        if (!isset($config['prefix']))
            $config['prefix'] = $this->get('database.default_prefix', '');
        $config['database'] = trim($config['path'], '/');
        $driver = 'Db_Driver_' . ucfirst($config['scheme']);
        if (!class_exists($driver)) {
            error(t('Database driver <b>%s</b> not found.', 'Database errors', ucfirst($config['scheme'])));
            return FALSE;
        }
        $this->driver = new $driver($config);
        if (!$this->driver->init()){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Show errors
     */
    public function showErrors() {
        $errors = $this->driver->getErrors();
        if (DEVELOPMENT && $errors) {
            error(implode('<br/>', $errors), t('Database error', 'Database'));
        }
    }

    /**
     * Flush database tables cache
     */
    public function index($action = NULL) {
        if (!page_access('db debug'))
            return;
        switch ($action) {
            case 'flush':
                $this->system_cache->removeTags('db_fields');
                break;
        }
    }

    /**
     * Output all queries
     */
    public function trace() {
        $tpl = new Template('Db.debug');
        $tpl->queries = $this->driver->getBenchmark();
        echo $tpl->render();
    }

}
