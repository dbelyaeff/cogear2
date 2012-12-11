<?php

/**
 *  Simple object
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
abstract class Object extends Adapter {

    protected $object;

    /**
     * Конструктор
     *
     * @param array $data
     */
    public function __construct($options = NULL, $place = NULL) {
        $this->object(new Core_ArrayObject());
        parent::__construct($options, $place);
    }

    /**
     * Set current object
     *
     * @param array|ArrayObject $data
     */
    public function object($data = NULL) {
        if ($data) {
            $this->object = is_object($data) ? $data : Core_ArrayObject::transform($data);
        }
        else {
            return $this->object;
        }
    }

}