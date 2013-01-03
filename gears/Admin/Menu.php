<?php
/**
 * Admin menu
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Admin_Menu extends Menu_Auto{
    /**
     * Конструктор
     */
    public function __construct() {
        parent::__construct('admin', 'Admin/templates/menu',Url::gear('admin'));
    }

}