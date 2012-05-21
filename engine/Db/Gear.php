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
    protected $order = 0;
    public static $error_codes = array(
        100 => 'Driver not found.',
        101 => 'Couldn\'t connect to the database.',
    );

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        if ($dsn = config('database.dsn')) {
            if (!$this->checkDSN($dsn)) {
                $this->object->error(t('Couldn\'t establish database connection.', 'Db.errors'));
                fatal_error($this->object->errors());
            } else {
                hook('done', array($this, 'showErrors'));
                hook('footer', array($this, 'trace'));
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
            $config = array_merge($config,$query);
        }
        if (!isset($config['host']))
            $config['host'] = 'localhost';
        if (!isset($config['user']))
            $config['user'] = 'root';
        if (!isset($config['pass']))
            $config['pass'] = '';
        if (!isset($config['prefix']))
            $config['prefix'] = $this->get('database.prefix', '');
        $config['database'] = trim($config['path'], ' /');
        $adapter = 'Db_Driver_' . ucfirst($config['scheme']);
        if (!class_exists($adapter)) {
            error(t('Database driver <b>%s</b> not found.', 'Database errors', ucfirst($config['scheme'])));
            return FALSE;
        }
        $this->attach(new $adapter($config));
        if (!$this->object->init()){
            return FALSE;
        }
        cogear()->db = $this->object;
        return TRUE;
    }

    /**
     * Show errors
     */
    public function showErrors() {
        $errors = $this->object->getErrors();
        if (config('site.development') && $errors) {
            error(implode('<br/>', $errors), t('Database error', 'Database'));
        }
    }

    /**
     * Flush database tables cache
     */
    public function index($action = NULL) {
        if (!page_access('db.debug'))
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
        $tpl->data = $this->getBenchmark();
        $tpl->show('footer');
    }

}
