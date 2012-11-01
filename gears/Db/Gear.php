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
    protected $is_core = TRUE;
    protected $hooks = array(
        'done' => 'showErrors',
    );

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        if(cogear()->db) return;
        if ($dsn = config('database.dsn')) {
            $this->object(new Db($dsn));
            if (access('Dev')) {
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
        if ($errors = $this->object()->getErrors()) {
            foreach($errors as $key=>$error){
                $errors[$key] = t($error,'Db');
            }
            error(implode('<br/>', $errors), t('Database error', 'Db'));
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
        $tpl = new Template('Db/templates/debug');
        $tpl->data = $this->getBenchmark();
        $tpl->show('footer');
    }

}
