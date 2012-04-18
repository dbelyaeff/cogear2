<?php

/**
 *  Redactor WYSIWYG editor 
 * 
 *  http://imperavi.ru/redactor/
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Redactor_Editor extends Wysiwyg_Abstract {

    /**
     * Load scripts
     */
    public function load() {
        $folder = cogear()->redactor->folder . '/js/redactor/';
        css($folder . 'css/redactor.css');
        js($folder . 'redactor.min.js','after');
        inline_js("$(document).ready(
		function()
		{
			$('[name=".$this->name."]').redactor();
		}
	);",'after');
    }
}
