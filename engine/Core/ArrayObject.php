<?php

/**
 * Successor of ArrayObject that creates new instances of itself as vars of itself in case if doesn't exits.
 *
 * If you simpy call $config->prop and prop doesn't exists — it'll be new Recursive ArrayObject.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Engine
 * @subpackage	Core
 * @version		$Id$
 */
class Core_ArrayObject extends ArrayObject {
    const BEFORE = 0;
    const AFTER = 1;

    /**
     * Constructor
     *
     * @param	array
     */
    public function __construct($data = array()) {
        parent::__construct($data, parent::ARRAY_AS_PROPS & parent::STD_PROP_LIST);
    }

    /**
     * Wake from serialization
     *
     * @param array $array
     */
    public static function __set_state($array) {
        $object = new self();
        foreach ($array as $key => $value) {
            $object->offsetSet($key, $value);
        }
        return $object;
    }

    /**
     * Transform any array into self object recursively.
     * If argument is Zend_Config object it will be transformed into array.
     *
     * @param	array|ZendConfig	$data
     * @return	ArrayObject
     */
    public static function transform($data) {
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_array($value)) {
                    $value = self::transform($value);
                }
            }
            return new self($data);
        }
        return $data;
    }

    /**
     * Mix elements with another object
     *
     * @param  array|self $data
     */
    public function mix($data) {
        if (!$data)
            return;
        $data instanceof self && $data = (array) $data;
        $data = self::transform(array_merge($this->toArray(), $data));
        /* Found some issue with PHP < 5.3
         * Object can't accept another instance of self for exchange
         *
         * $this->exchangeArray($data);
         *
         * Have to reinterpret exchangeArray method with simple foreach cycle
         */
        $this->adopt($data);
    }

    /**
     * Merge some data with existing
     *
     * @param array|object $data
     */
    public function adopt($data) {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->offsetSet($key, $value);
            }
        }
        return $this;
    }

    /**
     * Find element by value
     *
     * @param mixed $needle
     * @return  NULL|mixed  Null or found element key.
     */
    public function findByValue($needle) {
        foreach ($this as $key => $value) {
            if ($value == $needle) {
                return $key;
            }
        }
        return NULL;
    }

    /**
     * Magic __get method
     *
     * @param	string
     * @return	mixed
     */
    public function __get($name) {
        if (!$this->offsetExists($name)) {
            return NULL;
        }
        return $this->offsetGet($name);
    }

    /**
     * Magic __set method
     *
     * @param	string
     * @param	mixed
     */
    public function __set($name, $value) {
        $this->offsetSet($name, Core_ArrayObject::transform($value));
    }

    /**
     * Magic isset method
     *
     * @param	string	$name	Variable name
     * @return	boolean
     */
    public function __isset($name) {
        return $this->offsetExists($name);
    }

    /**
     * Detelte by offset
     *
     * @param string $name
     */
    public function __unset($name) {
        $this->offsetUnset($name);
    }

    /**
     * Prepend element — insert element at the beginning
     *
     * @param  mixed   $value
     */
    public function prepend($value, $key = NULL) {
        $temp_array = $this->getArrayCopy();
        if ($key) {
            $temp_array = array_merge(array($key=>$value),$temp_array);
        } else {
            array_unshift($temp_array, $value);
        }
        $this->exchangeArray($temp_array);
    }

    /**
     * Prepend to some key
     *
     * @param type $value
     * @param type $key
     * @param type $to
     */
    public function prependTo($value,$key,$to){
        $data = $this->getArrayCopy();
        $new = array();
        foreach($data as $k=>$item){
            if($k == $to){
                $new[$key] = $value;
            }
            $new[$k] = $item;
        }
        $this->exchangeArray(self::transform($new));
    }

    /**
     * Reverse object
     *
     * @return object
     */
    public function reverse() {
        return new Core_ArrayObject(array_reverse($this->toArray()));
    }

    /**
     * Exclude elements with the same keys from input $data
     *
     * @param array $data
     */
    public function differ($data) {
        $data instanceof self && $data = $data->toArray();
        $storage = $this->toArray();
        $this->exchangeArray(array_diff_key_recursive($storage, $data));
    }

    /**
     * Inject value at special position or before key
     *
     * @param   mixed   $value
     * @param   int|string     $position
     * @param   int $order
     */
    public function inject($value, $position=0, $order = NULL) {
        $order OR $order = self::BEFORE;
        $result = array();
        $it = $this->getIterator();
        $i = 0;
        while ($it->valid()) {
            if (is_numeric($position)) {
                if ($order == self::BEFORE && $position == $i) {
                    $result[] = $value;
                }
                $result[] = $it->current();
                if ($order == self::AFTER && $position == $i) {
                    $result[] = $value;
                }
            } elseif (is_string($position)) {
                $key = $position;
                if ($order == self::BEFORE && $position == $it->key()) {
                    $result[$key] = $value;
                }
                $result[$it->key()] = $it->current();
                if ($order == self::AFTER && $position == $it->key()) {
                    $result[$key] = $value;
                }
            }
            $it->next();
            $i++;
        }
        $this->exchangeArray($result);
    }

    /**
     * Place a piece of array at position
     *
     * @param array $array
     * @param int/string $position
     * @param int $order
     */
    public function place($array, $position=0, $order = NULL) {
        $order OR $order = self::BEFORE;
        $result = array();
        $it = $this->getIterator();
        is_string($position) OR $i = 0;
        while ($it->valid()) {
            $key = isset($i) ? $i++ : $it->key();
            $order == self::AFTER && $result[$key] = $it->current();
            if ($position == $it->key()) {
                foreach ($array as $k => $value) {
                    $k = isset($i) ? $i++ : $k;
                    $result[$k] = $value;
                }
            }
            $order == self::BEFORE && $result[$key] = $it->current();
            $it->next();
        }
        $this->exchangeArray($result);
    }

    /**
     * Slice a piece of iterable
     *
     * @param int $from
     * @param int $length
     */
    public function slice($from, $length = NULL) {
        $copy = $this->toArray();
        return new Core_ArrayObject(array_slice($copy, $from, $length));
    }

    /**
     * Simple wrapper for getArrayCopy method
     */
    public function toArray($result = array()) {
        foreach ($this as $key => $value) {
            $result[$key] = $value instanceof self ? $value->toArray() : $value;
        }
        return $result instanceof self ? $result->getArrayCopy() : $result;
    }

    /**
     * Returns data in serialized form
     *
     * @param  string  $glue
     * @return string
     */
    public function toString($glue="\n") {
        return implode($glue, $this->getArrayCopy());
    }

    /**
     * Real toString function
     *
     * @return  string
     */
    public function __toString() {
        return implode("\n", $this->getArrayCopy());
    }

    /**
     * Render object
     *
     * @return string
     */
    public function render() {
        return $this->toString();
    }

    /**
     *
     *
     * @param int $position
     */
    public function show($region = 'content', $position = 0, $where = 0) {
        $position ? inject($region, $this->render(), $position, $where) : append($region, $this->render());
    }

    /**
     * Sort by order
     *
     * @param	object	$a
     * @param	object	$b
     * @return	int
     */
    public static function sortByOrder($a, $b) {
        return self::sortBy('order', $a, $b);
    }

    /**
     * Sort by param
     *
     * @param string $param
     * @param object $a
     * @param object $b
     * @return int
     */
    public static function sortBy($param, $a, $b) {
        if(!is_object($a)){
            return 1;
        }
        elseif(!is_object($b)){
            return -1;
        }
        else if ($a->order == $b->order) {
            return 0;
        }
        return floatval($a->order) < floatval($b->order) ? -1 : 1;
    }

}

/**
 * Diff keys recursive
 *
 * @param array $a1
 * @param array $a2
 * @return array
 */
function array_diff_key_recursive($a, $b) {
    foreach ($a as $key => $value) {
        if (is_array($value) && isset($b[$key])) {
            if ($result = array_diff_key_recursive($value, $b[$key])) {
                $a[$key] = $result;
            } else {
                unset($a[$key]);
            }
        } elseif (isset($b[$key])) {
            unset($a[$key]);
        }
    }
    return sizeof($a) ? $a : NULL;
}