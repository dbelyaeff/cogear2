<?php

/**
 * WYSIWYG gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Wysiwyg_Gear extends Gear {

    protected $name = 'WYSIWYG';
    protected $description = 'Visual editors manager.';
    public static $editors = array(
        'redactor' => 'Redactor_Editor',
        'markitup' => 'Markitup_Editor',
        'textarea' => 'Form_Element_Textarea',
    );
    protected $order = -10;

    /**
     * Init
     */
    public function init() {
        parent::init();
        Form::$types['editor'] = self::$editors[config('wysiwyg.editor', 'textarea')];
    }

    /**
     * Control Panel
     */
    public function admin() {
        $form = new Form("Wysiwyg.config");
        $options = new Core_ArrayObject;
        $options->editor = config('wysiwyg.editor');
        $form->init();
        $form->elements->type->setValues(self::$editors);
        $form->object($options);
        if ($result = $form->result()) {
            if (isset(self::$editors[$result['type']])) {
                cogear()->set('wysiwyg.editor', $result['type']);
                success('Configuration saved successfully.');
            }
        }
        append('content', $form->render());
    }

}