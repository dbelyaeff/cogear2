<?php
/**
 * Comments views
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Comments_Views extends Db_ORM {
    protected $table = 'comments_views';
    protected $primary = 'id';

    /**
     * Insert data
     *
     * @param array $data
     */
    public  function insert($data = NULL){
        if($result = parent::insert($data)){
            cogear()->session->remove('comments_views');
        }
        return $result;
    }
}