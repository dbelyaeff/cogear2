<?php
/**
 * 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Test_Gear extends Gear {
    protected $name = 'Test';
    protected $description = 'Gear for testing.';

    public function index($action=NULL, $subaction = NULL) {
        $image = new Image($this->dir . DS . 'images' . DS . 'siamese.cat.jpg');
        $image->options->maintain_ratio = FALSE;
        $image->resize('500')->render();
    }

}