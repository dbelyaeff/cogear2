<?php
/**
 * Comments gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Comments_Object extends Db_Tree {
    protected $table = 'comments';
    protected $template = 'Comments.item';
    protected $parent_field = 'reply';
}