<?php

/**
 * Code Editor
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Code_Editor extends Wysiwyg_Abstract {
    public $editor = array(
        'mode' => 'php',
        'theme' => 'elegant',
        'lineNumbers' => TRUE,
    );
    /**
     * Load scripts
     */
    public function load() {
        $this->editor = new Core_ArrayObject($this->editor);
        $folder = cogear()->code->folder . '/codemirror/lib/';
        css($folder . 'codemirror.css','form.close');
        js($folder . 'codemirror.js','form.close');
        $this->options->toolbar && $this->editor->extend($this->options->toolbar);
        $options = array(
            'mode' => $this->editor->mode,
            'theme' => $this->editor->theme,
            'lineNumbers' => $this->editor->lineNumbers,
        );
        inline_js("$(document).ready(
		function()
		{
			CodeMirror.fromTextArea(document.getElementById('{$this->getId()}-element'),".json_encode($options).");
		}
	);",'form.close');
    }
}
