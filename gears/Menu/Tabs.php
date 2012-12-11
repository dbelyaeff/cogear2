<?php
/**
 * Menu Tabs
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Menu_Tabs extends Menu_Auto{
    /**
     * Конструктор
     */
    public function __construct($options) {
        isset($options['template']) OR $options['template'] = 'Bootstrap/templates/tabs';
        parent::__construct($options);
    }
    
}