<?php

/**
 *  Form Element Submit
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Delete extends Form_Element_Submit {
    protected $type = 'submit';
    /**
     * Render
     * 
     * @return type 
     */
    public function render() {
        $this->prepareOptions();
        $this->options->value = $this->label;
        $this->options->label = '';
        $tpl = new Template('Form/templates/delete');
        $tpl->options = $this->options;
        $this->code = $tpl->render();
        return $this->code;
    }

}
