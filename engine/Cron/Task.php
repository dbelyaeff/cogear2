<?php
/**
 * Cron task
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Cron
 * @subpackage          
 * @version		$Id$
 */
class Cron_Task extends Db_ORM{
    // Notice. All commented fields are represented by the identicaly named database fields in records.
    
    /**
     * Every task should have it's own name
     * 
     * @var string 
     */
    //protected $name;
    /**
     * Callback
     * 
     * @var objcet
     */
    //protected $callback;
    /**
     * Period to execute task
     * 
     * If type of task is ONCE → period must be exact time in UNIX time format.
     * If type of task is PERIODICAL → period must be span of time (like timer countdown) in UNIX time format.
     * 
     * @var int
     */
    //protected $period = 3600;
    /**
     * IN filters
     * 
     * @var array 
     */
    protected $filters_in = array(
        'callback' => array('serialize'),
        'args' => array('serialize'),
    );
    /**
     * OUT filters
     * 
     * @var array 
     */
    protected $filters_out = array(
        'callback' => array('unserialize'),
        'args' => array('unserialize'),
    );

    /**
     * New task contructor
     * 
     * @param string    $name
     */
    public function __construct($name = NULL){
        parent::__construct('cron');
        $name && $this->name = $name;
        $name && $this->find();
    }
    
    /**
     * Run task
     */
    public function run(){
        $this->callback->call($this->args);
        $this->last_exec = time();
        $this->save();
        // Make log
        $log = new Db_ORM('cron_log');
        $log->cid = $this->id;
        $log->exec_time = $this->last_exec;
        $log->save();
    }
}