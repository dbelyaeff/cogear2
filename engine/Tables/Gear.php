<?php

/**
 * Install gear
 *
 * @author		Naumov Aleksandr <inetlover@gmail.com>
 * @copyright		Copyright (c) 2011, Naumov Aleksandr
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Tables
 * @version		$Id$
 */
class Tables_Gear extends Gear {

    protected $name = 'Tables';
    protected $description = 'Tables for the data.';
    protected $type = Gear::MODULE;
    protected $order = 0;

    const PARAMS = '?';
    const PATH = '/';

}
