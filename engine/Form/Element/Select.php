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
    protected $type = 'select';
    protected $values = array();
    protected $callback;
    /**
     * Set values
     *  
     * @param array $data 
     */
    public function setValues($data){
        $this->values = $data;
    }

    public function render(){
        if($this->callback){
            $callback = Callback::prepare($this->callback);
            is_callable($callback) && $this->setValues(call_user_func($callback));
        }
        $this->getAttributes();
        $code[] = HTML::open_tag('select', $this->attributes);
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
            $tpl->assign($this->attributes);
            $tpl->code = $code;
            $code = $tpl->render();
        }
        return $code;
    }
}

