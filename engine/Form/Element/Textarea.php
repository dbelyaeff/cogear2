<?php
/**
 *  Form Element Textarea
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Textarea extends Form_Element_Abstract{
    /**
     * Render
     *
     * @return string
     */
    public function  render() {
        $code = HTML::paired_tag('textarea', $this->value, $this->prepareOptions());
        if ($this->wrapper) {
            $tpl = new Template($this->wrapper);
            $tpl->assign($this->options);
            $tpl->code = $code;
            $code = $tpl->render();
        }
        return $code;
    }
}