<?php
/**
 * Form option abstract
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Form_Option_Abstract {
    protected $element;
    /**
     * Initialization
     *
     * @param Form_Element_Abstract $element
     */
    public function init(Form_Element_Abstract $element) {
        $this->element = $element;
    }
}