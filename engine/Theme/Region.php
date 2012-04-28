<?php
/**
 * Theme region
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Theme
 * @subpackage          
 * @version		$Id$
 */
class Theme_Region extends Core_ArrayObject{
    /**
     * Render theme region
     * 
     * @return string 
     */
    public function render(){
        return $this->toString();
    }
}