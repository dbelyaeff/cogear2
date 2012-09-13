<?php
/**
 *  Form Element Link
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Link extends Form_Element_Abstract{
    /**
     * Constructor
     * 
     * @param type $options 
     */
    public function __construct($options) {
        $options['template'] = 'Form.link';
        $options['wrapper'] = FALSE;
        parent::__construct($options);
    }
}
