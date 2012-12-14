<?php

/**
 * Шестеренка редактора MarkItUp
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Markitup_Gear extends Gear {

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        Wysiwyg_Gear::$editors[ 'markitup'] = 'Markitup_Editor';
    }


    /**
     * Skip assets loading
     */
    public function loadAssets() {
//        parent::loadAssets();
    }

}