<?php

/**
 *  Form Element Title
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Title extends Form_Element_Abstract {
    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        $this->code = HTML::paired_tag('h1',$this->label);
        return $this->code;
    }

}