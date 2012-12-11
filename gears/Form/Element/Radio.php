<?php

/**
 *  Form Element Radio
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Radio extends Form_Element_Abstract {

    protected $type = 'radio';
    protected $values = array();
    protected $callback;

    /**
     * Set values
     *
     * @param array $data
     */
    public function setValues($data) {
        $this->values = $data;
    }

    public function render() {
        if ($this->callback) {
            $callback = Cogear::prepareCallback($this->callback);
            $this->setValues(call_user_func($callback));
        }
        $this->setAttributes();
        $code = array();
        foreach ($this->values as $key => $value) {
            $attributes = $this->options;
            $attributes['value'] = $key;
            if ($key === $this->value) {
                $attributes['checked'] = 'checked';
            }
            $code[] = HTML::tag('input', $attributes).$value;
        }
        $code = implode("<br/>", $code);
        if ($this->wrapper) {
            $tpl = new Template($this->wrapper);
            $tpl->assign($this->options);
            $tpl->code = $code;
            $code = $tpl->render();
        }
        return $code;
    }

}
