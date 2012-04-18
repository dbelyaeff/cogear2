<?php

/**
 * Validate user email
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          User
 * @version		$Id$
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
            $this->element->addError(t('Email is already taken!'));
            return FALSE;
        }
        return TRUE;
    }

}