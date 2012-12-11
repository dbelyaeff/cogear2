<?php

/**
 * Lenth validator
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

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
            return $this->element->addError(t('Поле должно иметь значение длиной от %d до %d симолов.', $from, $to));
        } elseif ($from && $length < $from) {
            return $this->element->addError(t('Поле должно иметь значение длинее, чем %d символов.', $from));
        } elseif ($to && $length > $to) {
            return $this->element->addError(t('Поле должно иметь значение не длиннее, чем %d символов.', $to));
        }
        return TRUE;
    }

}

