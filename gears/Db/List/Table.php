<?php

/**
 * Abstract database list
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
abstract class Db_List_Table extends Db_List_Abstract {

    protected $fields;

    /**
     * Get fields for table
     *
     * @return  Core_ArrayObject
     */
    abstract public function getFields();

    /**
     * Set fields for table
     *
     * @param   mixed   $fields
     * @return  Core_ArrayObject
     */
    public function setFields($fields) {
        if (is_array($fields)) {
            $fields = Core_ArrayObject::transform($fields);
        }
        return $this->fields = $fields;
    }

    /**
     * Render process
     *
     * @return string
     */
    public function process($items, $pager) {
        $table = new Table(array(
                    'name' => $this->name,
                    'class' => 'table table-bordered table-striped shd',
                    'fields' => $this->getFields(),
                ));
        $table->object($items);
        return $table->render() . $pager->render();
    }

    /**
     * Prepare fields for table
     *
     * @param type $user
     * @return type
     */
    abstract public function prepareFields($item, $key);
}