<?php
/**
 *  Form Element Textarea
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

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