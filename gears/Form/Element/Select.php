<?php

/**
 * Form Element Select
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Select extends Form_Element_Abstract {

    /**
     * Set values
     *
     * @param array $data
     */
    public function setValues($data) {
        $this->options->values = $data;
    }

    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        if ($this->options->disabled) {
            return NULL;
        }
        $method = strtolower($this->form->method);
        $name = str_replace('[]', '', $this->name);
        $this->value = cogear()->input->$method($name);
        $this->filtrate();
        $result = $this->validate() ? $this->value : FALSE;
        return $result;
    }

    /**
     *
     * @return type
     */
    public function render() {
        if ($this->callback) {
            $callback = new Callback($this->callback);
            if ($callback->check()) {
                $this->options->values = $callback->run(array($this->form));
            }
        }
        $this->prepareOptions();
        $code[] = HTML::open_tag('select', $this->options);
        foreach ($this->values as $key => $value) {
            $attributes = array();
            if ($this->value instanceof Core_ArrayObject) {
                $this->value = $this->value->toArray();
            }
            if (is_array($this->value)) {
                if (in_array($key, $this->value)) {
                    $attributes['selected'] = 'selected';
                }
            } elseif ($key == $this->value) {
                $attributes['selected'] = 'selected';
            }
            $attributes['value'] = $key;
            $code[] = HTML::paired_tag('option', $value, $attributes);
        }
        $code[] = HTML::close_tag('select');
        $code = implode("\n", $code);
        if ($this->wrapper) {
            $tpl = new Template($this->wrapper);
            $tpl->assign($this->options);
            $tpl->code = $code;
            $code = $tpl->render();
        }
        return $code;
    }

}

