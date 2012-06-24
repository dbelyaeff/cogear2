<?php

/**
 * Pages list
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Pages_List extends Db_List {
    protected $class = 'Pages_Object';
    public $options = array(
        'dragndrop' => TRUE,
        'tree' => TRUE,
        'render' => TRUE,
    );

}