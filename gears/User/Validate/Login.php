<?php
/**
 * Validate user login
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         User

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
        $user = new User();
        $user->login = $value;
        $user->find();
        if ($user->id) $this->element->addError(t('Login name already in use!'));
        return $user->id ? FALSE : TRUE;
    }
}