<?php

/**
 * Ajax gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage  	jQuery
 * @version		$Id$
 */
class Ajax_Gear extends Gear {

    protected $name = 'Ajax';
    protected $description = 'Handle ajax requests.';
    protected $type = Gear::CORE;
    protected $order = 0;

    const PARAMS = '?';
    const PATH = '/';
    
    /**
     * Form ajax request via params
     * 
     * @param array $params 
     * @return string
     */
    public static function link($params, $prefix = self::PARAMS) {
        return '#' . $prefix . http_build_query($params);
    }

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
     * Get ajax param
     * 
     * @param   string|array  $name
     * $param   mixed   $default
     */
    public static function get($name, $default = NULL) {
        if (is_array($name)) {
            $result = array();
            foreach ($name as $value) {
                $result[$value] = self::get($value);
            }
            return $result;
        }
        if (self::is() && isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        return $default;
    }

    /**
     * Send JSON response
     * 
     * @param array $data 
     */
    public static function json($data) {
        echo json_encode($data);
        event('exit');
        event('ajax.exit');
        exit();
    }
    /**
     * Send denied response
     */
    public static function denied(){
        self::json(array('message'=>array(
            'class' => 'error',
            'body' => t('You don\'t have enough priveleges to execute this procedure.'),
        )));
    }

}