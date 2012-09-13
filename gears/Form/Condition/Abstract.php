<?php
/**
 * Abstract form element show condition class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
abstract class Form_Condition_Abstract {
	/**
	 * Form
	 *
	 * @object
	 */
	protected $form;
        /**
         * Options
         *
         * @array
         */
        public $options;
        /**
         * Element
         *
         * @var Form_Element_Abstract
         */
        public $element;
        /**
	 * Constructor
	 *
	 * @param	object	$form
	 */
	public function __construct($options = array()){
                $this->options = $options;
                $cogear = getInstance();
		$this->form = $cogear->form->getForm();
	}
	/**
	 * Check
	 *
	 * @return	boolean
	 */
	abstract function check(); 
} 