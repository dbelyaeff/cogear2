<?php
/**
 * Core Array
 *
 * 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 *
 * @version		$Id$
 */
class Core_Array {
	/**
	 * Transform multidimensional array to plain using key join with separator
	 * Example:
	 * array(3) {
	 *	  ["some"] => array(1) {
	 *	    ["var"] => string(10) "Some value"
	 *	  }
	 *	  ["second"] => array(1) {
	 *	    ["var"] => string(10) "Some value"
	 *	  }
	 *	  ["third"] => array(1) {
	 *	    ["var"] => array(1) {
	 *	      ["subvar"] => string(10) "Some value"
	 *	    }
	 *	  }
	 *	}
         *
	 *
	 *		 array(3) {
	 *		  ["some.var"] => string(10) "Some value"
	 *		  ["second.var"] => string(10) "Some value"
	 *		  ["third.var.subvar"] => string(10) "Some value"
	 *		}
         *
	 * @param	array	$data
	 * @param	string	$separator
	 * @return	array
	 */
	public static function multiToPlain($data,$separator = '.'){
	    $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($data),RecursiveIteratorIterator::SELF_FIRST);
	    $level = 0;
	    $full_key = '';
	    $result = array();
	    foreach($it as $key=>$value){
		    if($it->getDepth() > $level){
			    array_push($full_key,$key);
		    }
		    elseif($it->getDepth() == 0) {
				$full_key = array($key);		    
		    }
		    elseif($it->getDepth() == $level){
			    array_pop($full_key);
			    array_push($full_key,$key);
		    }
		    elseif($it->getDepth() < $level) {
			    $diff = $level - $it->getDepth();
				for($i = 0; $i <= $diff; $i++){
					array_pop($full_key);
				}
				array_push($full_key,$key);
		    }
		    if(!is_array($value) && !is_object($value)){
			    $result[implode($separator,$full_key)] = $value;
		    }
		    $level = $it->getDepth();
	    }
		return $result;
	}
	
	/**
	 * Transform plain array to multidimensional
	 * @param	array		$data
	 * @param	string	$separator
	 * @return	array
	 */
	public static function plainToMulti($data,$separator = '.'){
		$result = new Core_ArrayObject();
		foreach($data as $key=>$value){
			$pieces = new CachingIterator(new ArrayIterator(explode($separator,$key)));
		    $current =& $result;
		    foreach($pieces as $piece){
			    if($pieces->hasNext()){
				    if(!isset($current->$piece)){
					    $current->$piece = new Core_ArrayObject();
				    }
				    $current =& $current->$piece;
			    }
			    else {
				    $current->$piece = $value;
			    }
		    }
		}
		return $result->toArray();
	} 
	
	/**
	 * Replace plain keys using mask
	 *
	 * @param	array		$data
	 * @param	string	$mask
	 * @param	string	$replace_to
	 * @return	array
	 */
	public static function replaceKeyByMask($data = array(), $mask = '*', $replace_to = ''){
		$result = array();
		$mask = preg_quote($mask);
		$mask = str_replace(array('\*','%'),array('(.*?)','([^\.]+)'),$mask);
		if($data){
                    foreach($data as $key=>$value){
			$key = preg_replace('#'.$mask.'#',$replace_to,$key);
			if(!empty($key)){
				$result[$key] = $value;
			}
                    }
                }
		return $result;
	} 
} 