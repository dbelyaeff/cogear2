<?php
/**
 * Form Uri filter
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
class Form_Filter_Uri extends Form_Filter_Abstract {
	/**
	 * Filter
	 *
	 * @value
	 */
	public function filter($value,$length = NULL){
                // If field is empty
                if(!$value){
                    $value = cogear()->input->post('name');
                }
                // If even $_POST['name'] doesn't exist
                if(!$value){
                    return;
                }
                $value = transliterate($value);
                // Filter for all unsafe chars
                $value = preg_replace('#([^'.config('form.filter.uri.pattern','\w\._-').'])#','-',$value);
                // Replace ----- with single - and trim - by sides
                $value = trim(preg_replace('#([-]{2,})#','-',$value),'-');
                $length OR $length = config('form.filter.uri.maxlength',15);
                if($length){
                    $value = substr($value, 0,$length);
                }
		return $value;
	}
}