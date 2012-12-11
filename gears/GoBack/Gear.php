<?php
/**
 * Шестеренка «Назад»
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class GoBack_Gear extends Gear {
    /**
     * Init
     */
    public function init() {
        parent::init();
        hook('form.render',array($this,'showGoBackButton'));
    }

    /**
     * Show GoBack button hook
     */
    public function showGoBackButton($Form){
       if(defined('LAYOUT')) return;
       $tpl = new Template('GoBack/templates/button');
       $tpl->show();
    }
}