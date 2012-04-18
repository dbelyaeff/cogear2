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
    public $object;
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($options = NULL,$place = NULL) {
        parent::__construct($options, $place);
        $this->object = new Core_ArrayObject();
    }
    /**
     * Set current object
     *
     * @param array|ArrayObject $data
     */
    public function attach($data){
        $this->object = is_object($data) ? $data : Core_ArrayObject::transform($data);
    }
    
    /**
     * Detach object
     */
    public function detach(){
        $this->object = NULL;
    }
}