<?php
/**
 * Twitter boostrap navbar
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Bootstrap

 */
class Bootstrap_Navbar extends Menu_Auto {
    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options) {
        $options['template'] = 'Bootstrap/templates/navbar';
        parent::__construct($options);
    }
}