<?php

/**
 *  Form Element Div
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Div extends Form_Element_Abstract {

    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $cogear = cogear();
        return NULL;
    }

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        $this->code = '<div class="'.$this->class.'">'.$this->label.'</div>';
        return $this->code;
    }

}