<?php
/**
 * Alphabet validator
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

 */
class Form_Validate_Name extends Form_Validate_Regexp{
	/**
	 * Конструктор
	 */
	public function __construct(){
                $regexp = '([a-zA-Zа-яА-Я\d]+)';
		parent::__construct($regexp,t('Значение поля может включать в себя только буквы и пробелы.'));
	}
}