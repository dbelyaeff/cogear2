<?php

/**
 *  Simple object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
abstract class Object extends Adapter {

    protected $object;

    /**
     * Constructor
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