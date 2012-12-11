<?php
/**
 *  Form Element Submit
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Element_Submit extends Form_Element_Button{
    /**
     * Конструктор
     *  
     * @param type $options 
     */
    public function __construct($options) {
        $options['wrapper'] = FALSE;
        parent::__construct($options);
    }
}
