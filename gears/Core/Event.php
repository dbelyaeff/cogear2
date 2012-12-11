<?php

/**
 * Event
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Event extends Core_ArrayObject {

    private $name;
    private $result;
    private $results;
    public static $current;

    /**
     * Конструктор
     */
    public function __construct($name) {
        $this->name = $name;
        parent::__construct();
        $this->results = new Core_ArrayObject();
    }

    /**
     * Run event
     *
     * @param   $args
     * @return  object
     */
    public function run($args) {
        self::$current = $this;
        foreach ($this as $callback) {
            $result = $callback->run($args);
            if (NULL !== $result) {
                $this->results->append($result);
            }
        }
        return $this;
    }

    /**
     * Get exec results
     *
     * @return Core_ArrayObject
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * Set result
     *
     * @param mixed $data
     * @return mixed
     */
    public function result($data = NULL) {
        if ($data) {
            return $this->result = $data;
        }
        return $this->result;
    }

    /**
     * Check if event has result
     *
     * @return boolean
     */
    public function check() {
        foreach ($this->results as $result) {
            if ($result == FALSE) {
                return FALSE;
            }
        }
        return TRUE;
    }

}
