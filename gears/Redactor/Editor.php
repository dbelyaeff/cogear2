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
//        'toolbar' => '/redactor/toolbar/post',
//        'autosave' => FALSE,
//        'interval' => 20,
//        'css' => 'style.css',
//        'visual' => true,
//        'fullscreen' => false,
//        'overlay' => true,
        'fixed' => true,
        'fixedTop' => 40,
    );
    /**
     * Load scripts
     */
    public function load() {
        $options = new Core_ArrayObject($this->editor);
        // If we have some options in form config — merge with them
        $this->options->editor && $options->extend($this->options->editor);
        $folder = cogear()->redactor->folder . '/';
        css($folder . 'css/redactor.css');
        js($folder . 'js/redactor.js','after');
        $options->lang = config('i18n.lang','en');
        js($folder . 'js/'.$options->lang.'.js','after');
        $options->buttons = array('html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|',
'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
'image', 'video', 'file', 'table', 'link', '|',
'fontcolor', 'backcolor', '|',
'alignleft', 'aligncenter', 'alignright', 'justify', '|',
'horizontalrule');
        $options->autoresize = TRUE;
        $options->imageUpload = l('/redactor/upload/');
        $options->overlay = TRUE;
        $options->minHeight = 300;
        $options->wym = FALSE;
        $options->autosave = FALSE;
        event('redactor.options',$options);
//        $this->options->toolbar && $this->editor->extend($this->options->toolbar);
//        $options = array(
//            'lang' => config('i18n.lang','en'),
//            'toolbar' => $this->editor->toolbar,
//            'autosave' => $this->editor->autosave,
//            'interval' => $this->editor->interval,
//            'css' => $this->editor->css,
//            'visual' => $this->editor->visual,
//            'fullscreen' => $this->editor->fullscreen,
//            'overlay' => $this->overlay,
//            'toolbar' => l($this->editor->toolbar),
//            'imageUpload' => l('/redactor/upload/image/'),
//        );
        inline_js("
$(document).ready(
		function()
		{
			document.redactor = $('[name=".$this->name."]').redactor(".stripcslashes(json_encode($options)).");
		}
	);",'after');
    }

    /**
     * Insert text
     *
     * @param type $text
     */
//    public static function insert($text){
//        inline_js("window.opener.document.redactor.execCommand(\"inserthtml\", \"".  Ajax::escape($text)."\");
//            window.close();");
//    }
}
