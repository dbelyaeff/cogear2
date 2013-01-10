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
class Form_Element_Textarea extends Form_Element_Abstract {
    /**
     * Конструктор
     *
     * @param type $options
     */
    public function __construct($options) {
        $options['template'] = 'Form/templates/textarea';
        parent::__construct($options);
    }

}