<?php

/**
 *  Form Element Title
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Title extends Form_Element_Div {

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        event('form.element.render', $this);
        event('form.element.' . $this->options->type . '.render', $this);
        $this->options->class = "page-header";
        $this->options->label = HTML::paired_tag('h2', $this->label);
        return parent::render();
    }

}