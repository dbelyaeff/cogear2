<?php
/**
 * Regexp validator
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

 */
class Form_Validate_Regexp extends Form_Validate_Abstract{
	/**
	 * Regexp
	 *
	 * @string
	 */
	protected $regexp;
        /**
         * Error message
         *
         * @var
         */
        protected $error_msg;

	/**
	 * Конструктор
	 *
	 * @param	string	$regexp
	 * @param	string	$error_msg
	 */
	public function __construct($regexp,$error_msg = ''){
		$this->regexp = $regexp;
		if($error_msg){
			$this->error_msg = $error_msg;
		}
	}
	/**
	 * Validate
	 *
	 * @return	boolean
	 */
	public function validate($value){
		if($value && !preg_match('#^'.$this->regexp.'$#iu',$value)){
			return $this->element->error($this->error_msg ? $this->error_msg : t('Указаное недопустимое значение.'));
		}
		return TRUE;
	}
}