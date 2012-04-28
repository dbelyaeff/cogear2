<?php
/**
 *  Form Element Input
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Tab extends Form_Element_Abstract{
    public static $tabs = array();
    public function render(){
        $output = '';
        if($this->form->tab_opened == NULL){
            hook('form.'.$this->form->name.'.render.after',array($this,'initTabs'));
            hook('Form.element.submit.render',array($this,'autoCloseTab'));
        }
        if($this->form->tab_opened){
            $output = '</div>';
        }
        else {
            $this->form->tab_opened = TRUE;
        }
        //id="tab-'.$this->name.'"
        $output .= '<div class="form-element tab">';
        self::$tabs[$this->name] = $this->label;
        return $output;
    }
    /**
     * Close tag from last tab container
     * 
     * @param Form_Element_Submit $Submit 
     */
    public function autoCloseTab($Submit){
        if($this->form->tab_opened){
            $Submit->code = '</div>'.$Submit->code;
            $this->form->tab_opened = FALSE;
        }
    }
    /**
     * Initiate tabs
     * 
     * @param string $code 
     */
    public function initTabs($form){
        $prepend = '<ul class="pills">'."\n";
        foreach(self::$tabs as $id=>$label){
            $prepend .= "\t".'<li id="tab-'.$id.'"><a href="#tab-'.$id.'">'.$label.'</a></li>'."\n";
        }
        $prepend .= "</ul>";
        $form->code = preg_replace('(<form([^>]*)>)','$0'.$prepend,$form->code);
        inline_js('$(document).ready(function(){
            $("ul.pills").cgTabs("#'.$this->form->getId().' > .tab",{
                handler: "li",
            });
        })');
       
    }
}