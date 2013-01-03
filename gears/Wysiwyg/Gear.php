<?php

/**
 * Шестерёнка Визуальный редактор
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Wysiwyg_Gear extends Gear {

    public static $editors = array(
        'textarea' => 'Form_Element_Textarea',
    );

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
        $form = new Form("Wysiwyg/forms/config");
        $options = new Core_ArrayObject;
        $options->editor = config('wysiwyg.editor');
        $form->type->setValues(self::$editors );
        $form->object($options);
        if ($result = $form->result()) {
            if (isset(self::$editors[$result['type']])) {
                cogear()->set('wysiwyg.editor', $result['type']);
                success(t('Конфигурация успешно сохранена.'));
            }
        }
        append('content', $form->render());
    }

}