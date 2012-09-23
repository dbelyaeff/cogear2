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
     * Send json reponse
     *
     * @param array $data
     */
    public function json($data = array(),$escape = FALSE){
        $data && $this->extend($data);
        $response = stripslashes(json_encode($this));
        $escape && $response = $this->escape($response);
        echo $response;
        event('done');
        event('ajax');
        exit();
    }

    /**
     * Show ajax message
     *
     * @param string $text
     * @param string $type
     * @param string $title
     */
    public function message($text,$type = 'success',$title = NULL){
        $this->messages OR $this->messages = new Core_ArrayObject();
        $this->messages->append(array(
            'body' => $text,
            'type' => $type,
            'title' => $title,
        ));
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