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
    private $mcrypt_cipher;
    private $mcrypt_mode;
    /**
     * Constructor
     */
    public function __contsruct(){
        parent::__construct();
        $cogear = getInstance();
        $this->mcrypt_cipher = $cogear->get('secure.mcrypt_cipher', MCRYPT_BLOWFISH);
        $this->mcrypt_mode = $cogear->get('secure.mcrypt_mode', MCRYPT_MODE_ECB);
    }
    /**
     * Encrypt data
     * 
     * @param mixed $data 
     */
    public function encrypt($data){
        return base64_encode(serialize($data));
        ////mcrypt_encrypt($this->mcrypt_cipher, cogear()->key(), serialize($data), $this->mcrypt_mode);
    }
    /**
     * Decrypt data
     * 
     * @param string $data 
     */
    public function decrypt($data){
        return unserialize(base64_decode($data));
        //unserialize(mcrypt_decrypt($this->mcrypt_cipher, cogear()->key(), $data, $this->mcrypt_mode));
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
