<?php
/**
 * Vocabularies list
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Taxonomy_List_Vocabularies extends Db_List {
    protected $class = 'Taxonomy_Vocabulary';
    protected $template = 'Taxonomy.list/vocabularies';
    public $options = array(
        'dragndrop' => FALSE,
        'render' => TRUE,
    );
}