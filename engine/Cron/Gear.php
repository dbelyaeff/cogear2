<?php

/**
 * Cron gear
 *
 * Scheduler tasks.
 * 
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Cron
 * @version		$Id$
 */
class Cron_Gear extends Gear {

    protected $name = 'Cron';
    protected $description = 'Perform periodial tasks.';
    protected $order = 0;
    protected $version = '0.1';
    // Cron won't be start up for often than STEP value in seconds
    const STEP = 60;
    protected $key;

    /**
     * Init
     */
    public function init() {
        parent::init();
        hook('ignite', array($this, 'check'));
        $this->key = $this->keyGen();
    }

    /**
     * Generate key
     */
    private function keyGen() {
        return md5(date('Y-m-d H') . cogear()->secure->key());
    }

    /**
     * Check cron
     */
    public function check() {
        if (time() - $this->get('cron.last_run') > self::STEP) {
            // Set cron execute time
            $this->set('cron.last_run', time());
            // It's highly important to run cron after server response will be sent to user
            hook('after', array($this, 'poorMansCron'));
        }
    }

    /**
     * Poor man's cron
     * 
     * Call cron process via image tag
     */
    public function poorMansCron() {
        echo '<img src="' . Url::gear('cron') . 'run/' . encrypt($this->key) . '">';
    }

    /**
     * Run cron via special url
     *  
     * @param type $key 
     */
    public function run_action($key = NULL) {
        $this->key == decrypt($key) && $this->run();
    }

    /**
     * Run cron
     */
    public function run() {
        $now = time();
        $cron_task = new Cron_Task();
        if ($tasks = $cron_task->findAll()) {
            // Discount all limits for cron to fill free
            @set_time_limit(0);
            @ignore_user_abort();
            foreach ($tasks as $task) {
                // If task hasn't been executed, it idles for first time
                if (!$task->last_exec) {
                    $task->last_exec = $now;
                    $task->save();
                } elseif ($now - $task->last_exec > $task->period) {
                    $cron_task->attach($task);
                    $cron_task->run();
                }
            }
        }
        $px = new Image(ENGINE . DS . 'Core' . DS . 'img' . DS . '1x1.gif');
        $px->render();
        cogear()->save();
        exit();
    }

    /**
     * Add new task
     * 
     * @param string    $name
     * @param callback $callback
     * @param array $params
     * @param int $period // 60 — every minute, 3600 — every hour and so on @ value in seconds
     */
    public function addTask($name, $callback, $args = array(), $period = 3600) {
        $task = new Cron_Task($name);
        $task->callback = new Callback($callback);
        $task->args = $args;
        $task->type = $type;
        $task->save();
    }

}