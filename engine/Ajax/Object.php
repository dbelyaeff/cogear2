<?php

/**
 * Ajax object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Ajax_Object extends Core_ArrayObject{


    /**
     * Check if the ajax request has been caught
     *
     * @return boolean
     */
    public static function is() {
        $cogear = getInstance();
        return $cogear->request->isAjax();
    }

    /**
     * Build query string
     *
     * @param array $args
     * @return  string
     */
    public static function query($args){
        return http_build_query($args);
    }


    /**
     * Send html response
     *
     * @param type $data
     */
    public function send() {
        echo $this;
        event('done');
        event('ajax');
        exit();
    }

    /**
     * Escape string
     *
     * @param   string  $data
     * @param   boolean $addslashes
     * @return  string
     */
    public static function escape($data,$addslashes = TRUE){
        $addslashes && $data = addslashes($data);
        $data = preg_replace("#([\n\r]+)#","\" + \n\"", $data);
        return $data;
    }



}