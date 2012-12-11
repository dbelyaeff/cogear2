<?php
/**
 * Word validator
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

 */
class Form_Validate_Num extends Form_Validate_Regexp{
	/**
	 * Конструктор
	 */
	public function __construct(){
		parent::__construct('(\d+)',t('Значение поля может быть только числовым.'));
	}
}