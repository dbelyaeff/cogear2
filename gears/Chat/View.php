<?php

/**
 * Chat view object
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Chat_View extends Db_Item {

    protected $table = 'chats_views';
    protected $primary = 'mid';

}