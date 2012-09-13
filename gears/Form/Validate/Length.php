<?php

/**
 * Lenth validator
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
class Form_Validate_Length extends Form_Validate_Abstract {
    /**
     * Validate
     *
     * @return	boolean
     */
    public function validate($value, $from = NULL, $to = NULL) {
        if(!$value) return TRUE;
        $length = strlen($value);
        if ($from && $to && ($length > $to OR $length < $from)) {
            return $this->element->addError(t('Value must be between %d and %d symbols length.', 'Form_Validate', $from, $to));
        } elseif ($from && $length < $from) {
            return $this->element->addError(t('Value must be longer that %d.', 'Form_Validate', $from));
        } elseif ($to && $length > $to) {
            return $this->element->addError(t('Value must be no longer that %d.', 'Form_Validate', $to));
        }
        return TRUE;
    }

}

