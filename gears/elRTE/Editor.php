<?php

/**
 * elRTE Editor
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		elRTE
 * @subpackage          Wysiwyg
 * @version		$Id$
 */
class elRTE_Editor extends Wysiwyg_Abstract {

    protected $options = array(
        'styleWithCSS' => FALSE,
        'width' => '100%',
        'height' => 400,
        'toolbar' => 'complete',
    );

    /**
     * Load editor
     */
    public function load() {
        $path = Url::toUri(dirname(__FILE__)) . '/elrte-1.3/';
        css($path . 'css/elrte.full.css');
        js($path . 'js/elrte.full.js');
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render() {
        $this->options['lang'] = config('site.locale', 'en');
        extract((array)$this->options);
        cogear()->elfinder->load();
        inline_js("$(document).ready(
		function()
		{
                  var opts = {
                    lang: '{$lang}',
                    styleWithCSS: ".($styleWithCSS ? 'true' : 'false').",
                    width: '{$width}',
                    height: {$height},
                    toolbar: '{$toolbar}',
                    cssfiles : ['".Url::gear('elrte')."/css/elrte-inner.css'],
                    fmAllow: true,
                    fmOpen: function(callback){
                        $(\"<div id='{$this->getId()}-elfinder'>\").elfinder({
                            url: '" . Url::gear('elfinder') . "connector/',
                            lang: '{$lang}',
                            dialog : { width : 900, modal : true, title : '".t('Files')."' }, // открываем в диалоговом окне
                            closeOnEditorCallback : true, // закрываем после выбора файла
                            editorCallback : callback
                        })
                    }
                };
         $('#{$this->getId()} textarea').elrte(opts);
		}
	);");
        event('elRTE.load', $this);
        return parent::render();
    }

}