<?php
/**
 * Form Element Select
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Select extends Form_Element_Abstract{
    /**
     * Set values
     *
     * @param array $data
     */
    public function setValues($data){
        $this->values = $data;
    }

    /**
     *
     * @return type
     */
    public function render(){
        if($this->callback){
            $callback = new Callback($this->callback);
            if($callback->check()){
                $this->options->values = $callback->run(array($this->form));
            }
        }
        $this->prepareOptions();
        $code[] = HTML::open_tag('select', $this->options);
        foreach($this->values as $key=>$value){
            $attributes = array();
            if($key == $this->value){
                $attributes['selected'] = 'selected';
            }
            $attributes['value'] = $key;
            $code[] = HTML::paired_tag('option', $value, $attributes);
        }
        $code[] = HTML::close_tag('select');
        $code = implode("\n",$code);
        if ($this->wrapper) {
            $tpl = new Template($this->wrapper);
            $tpl->assign($this->options);
            $tpl->code = $code;
            $code = $tpl->render();
        }
        return $code;
    }
}

