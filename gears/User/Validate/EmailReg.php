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
class User_Validate_EmailReg extends Form_Validate_Abstract {

    /**
     * Validate user email.
     *
     * @param string $value
     */
    public function validate($value, $state = NULL){
        if(!$value) return TRUE;
        $user = new User();
        $user->email = $value;
        $user->where('login','',' != ');
        $user->find();
        if ($user->id) {
            $this->element->addError(t('Email is already taken!','User'));
            return FALSE;
        }
        return TRUE;
    }

}