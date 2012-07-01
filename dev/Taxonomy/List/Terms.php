<?php
/**
 * Vocabulary terms list
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Taxonomy_List_Terms extends Db_List {
    protected $class = 'Taxonomy_Term';
    public $options = array(
        'tree' => TRUE,
        'dragndrop' => TRUE,
        'render' => TRUE,
    );
}