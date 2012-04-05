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
class User_Validate_Email extends Form_Validate_Abstract{
    const EXCLUDE_SELF = 1;
    /**
     * Validate user email.
     * 
     * @param string $value 
     */
    public function validate($value, $state = NULL){
        if(!$value) return TRUE;
        switch($state){
            case self::EXCLUDE_SELF:
                return TRUE;
                break;
        }        
        $user = new Db_ORM('users');
        $user->email = $value;

	    $is_exist = (boolean)$user->find();
        if ($is_exist) $this->element->addError(t('Email is already taken!'));
        return !$is_exist;
    }
}