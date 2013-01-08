<?php

/**
 * Шестерёнка архиватора
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Zip_Gear extends Gear {

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            case 'create':
                return TRUE;
                break;
        }
        return FALSE;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        if (!class_exists('ZipArchive')) {
            error(t('Для того, чтобы система работа корректно, вам необходимо установить расширение ZIP для PHP или же использовать версию PHP выше 5.3.'));
        } else {
            $this->object(new ZipArchive());
        }
    }

}