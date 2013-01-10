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

    protected $routes = array(
        'admin/wysiwyg' => 'admin_action',
    );
    protected $access = array(
        'admin' => array(1),
    );
    public static $editors = array(
        'textarea' => 'Form_Element_Textarea',
    );

    /**
     * Инициализация
     */
    public function init() {
        parent::init();
        Form::$types['editor'] = isset(self::$editors[config('wysiwyg.editor', 'textarea')]) ? self::$editors[config('wysiwyg.editor', 'textarea')] : self::$editors['textarea'];
    }

    /**
     * Панель управления
     */
    public function admin_action() {
        $form = new Form("Wysiwyg/forms/config");
        $options = new Core_ArrayObject;
        $options->editor = config('wysiwyg.editor');
        $form->type->setValues(self::$editors);
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