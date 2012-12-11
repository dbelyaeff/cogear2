<?php

/**
 * Validate user email
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         User

 */
class User_Validate_Email extends Form_Validate_Abstract {
    const EXCLUDE_SELF = 1;
    /**
     * Validate user email.
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
        $user->email = $value;
        if ($user->find()) {
            if ($user->id == cogear()->user->id) {
                return TRUE;
            }
            $this->element->addError(t('Email is already taken!'));
            return FALSE;
        }
        return TRUE;
    }

}