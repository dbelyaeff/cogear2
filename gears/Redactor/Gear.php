<?php

/**
 * Шестеренка редактора Redactor
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Redactor_Gear extends Gear {
    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        Wysiwyg_Gear::$editors['redactor'] = 'Redactor_Editor';
    }


    /**
     * Пропускаем автозагрузку скриптов и стилей
     */
    public function loadAssets() {
//        parent::loadAssets();
    }
    /**
     * Активация шестерёнки
     *
     * @return type
     */
    public function enable(){
        $this->set('wysiwyg.editor','redactor');
        return parent::enable();
    }
}