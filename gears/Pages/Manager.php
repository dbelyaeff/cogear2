<?php
/**
 *  Pages manager
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Pages_Manager extends Db_ORM{
    /**
     * Constructor
     */
    public function  __construct() {
        parent::__construct('pages');
    }
    /**
     * Save
     */
    public function save(){
        if($result = parent::save()){
            $this->id = $result;
        }
    }
    /**
     * Delete
     */
    public function delete(){
        parent::delete();

    }
}
