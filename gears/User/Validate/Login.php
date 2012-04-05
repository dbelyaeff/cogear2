<?php
/**
 * Validate user login
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          User
 * @version		$Id$
 */
class User_Validate_Login extends Form_Validate_Abstract{
    const EXCLUDE_SELF = 1;
    /**
     * Validate user login.
     * 
     * @param string $value 
     */
    public function validate($value,$state = NULL){
        if(!$value) return TRUE;
        switch($state){
            case self::EXCLUDE_SELF:
                return TRUE;
                break;
        }        
        $user = new Db_ORM('users');
        $user->login = $value;
        
	    $finded = (boolean) $user->find();
        if ($finded) $this->element->addError(t('Login name already in use!'));
        return !$finded;
    }
}