<?php
/**
 * Secure gear
 * 
 * Helps to keep things secure.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Secure
 * @subpackage          
 * @version		$Id$
 */
class Secure_Gear extends Gear {

    protected $name = 'Secure';
    protected $description = 'Helps to keep things secure.';
    protected $order = 0;
    protected $hooks = array(
        'gear.request' => 'checkRequest',
    );
    /**
     * Constructor
     */
    public function __contsruct(){
        parent::__construct();
    }
    /**
     * Encrypt data
     * 
     * @param mixed $data 
     */
    public function encrypt($data){
        return base64_encode(serialize($data));
    }
    /**
     * Decrypt data
     * 
     * @param string $data 
     */
    public function decrypt($data){
        return unserialize(base64_decode($data));
    }
    
    /**
     * Gen or check secure key
     * 
     * @param   string  $key
     */
    public function key($key = NULL){
        if($key){
            return $key == $this->key();
        }
        else {
            // Get the key
            $key = config('secure.key',md5(date('H d.m.Y')));
            // Glue key with current ip
            $key = md5($key.$this->request->get('ip'));
            $key = substr($key, 0,5);
            return $key;
        }
    }
    
    /**
     * Check request for security hash
     */
    public function checkRequest(){
        if($s = $this->input->get(Url::SECURE)){
            if(!$this->key($s)){
                flash_error(t('You secret key doesn\'t match the original. Please, try once again,'),t('Warning'));
                back();
            }
        }
    }
}
/**
 * Encrypt data
 * 
 * @param mixed $data
 * @return string 
 */
function encrypt($data){
    return cogear()->secure->encrypt($data);
}
/**
 * Decrypt data
 * 
 * @param mixed $data
 * @return string 
 */
function decrypt($data){
    return cogear()->secure->decrypt($data);
}
