<?php

/**
 * Pages list
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Pages_List extends Db_List {
    protected $class = 'Pages_Object';
    public $options = array(
        'dragndrop' => TRUE,
        'tree' => TRUE,
        'render' => TRUE,
    );

}