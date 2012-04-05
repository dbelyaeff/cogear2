<?php

/**
 *  Form Element Div
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Div extends Form_Element_Abstract {

    protected $type = 'div';
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
        $this->getAttributes();
        $this->code = HTML::paired_tag('div',$this->value);
        return parent::render();
    }

}