<?php
/**
 *  Form Element Fieldset
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Fieldset extends Form_Element_Group{
    /**
     * Конструктор
     *
     * @param type $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->options->template = 'Form/templates/fieldset';
    }
}
