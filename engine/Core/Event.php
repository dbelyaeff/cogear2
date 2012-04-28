<?php

/**
 * Event
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Event extends Core_ArrayObject {
    private $is_stopped;

    /**
     * Stop event execution
     */
    public function stop() {
        $this->is_stopped = TRUE;
    }
    
    /**
     * Continue event execution
     */

    public function start() {
        $this->is_stopped = FALSE;
    }

    /**
     * Check if event has been stopped
     * 
     * @return boolean|null
     */
    public function is_stopped() {
        return $this->is_stopped;
    }

}
