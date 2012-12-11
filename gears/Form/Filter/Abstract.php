<?php

/**
 * Form filter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

 */
abstract class Form_Filter_Abstract extends Form_Option_Abstract{
    /**
     * Filter value
     *
     * @param	mixed   $value
     * @return  mixed   Filered data.
     */
    abstract function filter($value);
}

