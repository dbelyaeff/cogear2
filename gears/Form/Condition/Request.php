<?php
/**
 * Object condition
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
class Form_Condition_Object extends Form_Condition_Abstract {
	/**
	 * Check
	 *
	 * @param	array	$options
	 * @return	boolean
	 */
	public function check(){
		foreach($this->options as $key){
			if(!$this->form->$key->requestIsFetched){
				return FALSE;
			}
		}
		return TRUE;
	} 
} 