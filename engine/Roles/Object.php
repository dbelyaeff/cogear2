<?php
/**
 * User roles object
 * 
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Roles_Object extends Db_ORM {
    protected $table = 'roles';
    protected $filters_out = array(
        'name' => 'translateName',
    );
    
    /**
     * Translate role name
     * 
     * @param string $name
     * @return string 
     */
    public function translateName($name){
        return t($name,'Roles');
    }
}