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
    public $editor = array(
        'toolbar' => 'post',
        'autosave' => FALSE,
        'interval' => 20,
        'css' => 'blank.css',
        'visual' => true,
        'fullscreen' => false,
        'overlay' => true,

    );
    /**
     * Load scripts
     */
    public function load() {
        $this->editor = new Core_ArrayObject($this->editor);
        $folder = cogear()->redactor->folder . '/js/redactor/';
        css($folder . 'css/redactor.css');
        js($folder . 'redactor.js','after');
        $this->options->editor && $this->editor->mix($this->options->editor);
        $options = array(
            'lang' => config('i18n.lang','en'),
            'toolbar' => $this->editor->toolbar,
//            'path' => cogear()->redactor->folder.'/js/',
            'autosave' => $this->editor->autosave,
            'interval' => $this->editor->interval,
            'css' => $this->editor->css,
            'visual' => $this->editor->visual,
            'fullscreen' => $this->editor->fullscreen,
            'overlay' => $this->overlay,
            'imageUpload' => l('/redactor/upload/image/'),
        );
        inline_js("
$(document).ready(
		function()
		{
			document.redactor = $('[name=".$this->name."]').redactor(".json_encode($options).");
		}
	);",'after');
    }

    /**
     * Insert text
     *
     * @param type $text
     */
    public static function insert($text){
        inline_js("window.opener.document.redactor.execCommand(\"inserthtml\", \"".  Ajax::escape($text)."\");
            window.close();");
    }
}
