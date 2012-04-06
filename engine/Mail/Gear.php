<?php

/**
 * Mail gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Mail_Gear extends Gear {

    protected $name = 'Mail';
    protected $description = 'Helps to send emails.';

    public function test_action() {
        $mail = new Mail('root@localhost', 'admin', array('admin@cogear.ru'), 'Test', 'Body');
        $mail->send();
    }

}