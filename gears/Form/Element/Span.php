<?php

/**
 *  Form Element Span
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Span extends Form_Element_Abstract {

    protected $type = 'span';
    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $cogear = getInstance();
        return NULL;
    }

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        $this->code = HTML::paired_tag('span',$this->value);
        return parent::render();
    }

}