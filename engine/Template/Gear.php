<?php
/**
 * Template gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Template_Gear extends Gear {
    protected $name = 'Template';
    protected $description = 'Deals with templates';
}


/**
 * Template object alias
 *
 * @param string $name
 * @param array $args
 * @return Template
 */
function template($name,$args = array()){
    $tpl = new Template($name);
    $args && $tpl->assign($args);
    return $tpl;
}