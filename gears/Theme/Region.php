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
class Theme_Region extends Options{
    /**
     * Render theme region
     *
     * @return string
     */
    public function render(){
        $output = new Core_ArrayObject();
        foreach($this as $item){
            if($item instanceof Callback){
                $output->append($item->run());
            }
            else {
                $output->append($item);
            }
        }
        event('theme.region',$this,$output);
        event('theme.region.'.$this->name,$this,$output);
        return $output;
    }
}