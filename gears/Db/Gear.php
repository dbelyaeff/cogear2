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
    protected $is_core = TRUE;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        if(cogear()->db) return;
        if ($dsn = config('database.dsn')) {
            $this->object(new Db($dsn));
            if (access('Dev') && config('site.development')) {
                hook('done', array($this, 'showErrors'));
                hook('footer', array($this, 'trace'));
            }
        } else {
            error(t('Database connection string is not defined.', 'Db.errors'));
        }
    }

    /**
     * Show errors
     */
    public function showErrors() {
        $errors = $this->object()->getErrors();
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
