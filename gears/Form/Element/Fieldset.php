<?php
/**
 *  Form Element Fieldset
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Fieldset extends Form_Element_Group{
    /**
     * Constructor
     *
     * @param type $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->options->template = 'Form/templates/fieldset';
    }
}
