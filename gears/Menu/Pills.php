<?php
/**
 * Menu Pills
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Menu_Pills extends Menu_Auto{
    /**
     * Конструктор
     */
    public function __construct($options) {
        isset($options['template']) OR $options['template'] = 'Bootstrap/templates/pills';
        parent::__construct($options);
    }

}