<?php

/**
 *  elFinder object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Files_Manager extends Options {

    /**
     * Load elFinder scripts
     */
    public function load() {
        $folder = cogear()->files->folder . '/';
        css($folder . 'css/elfinder.min.css');
        css($folder . 'css/theme.css');
        js($folder . 'js/elfinder.min.js');
        js($folder . 'js/i18n/elfinder.' . config('i18n.lang') . '.js');
    }

    /**
     * Render
     */
    public function render() {
        $this->load();
        inline_js("$(document).ready(function(){
                var elf = $('#elfinder').elfinder({
                        url : '" . l('/files/connector/') . "',
                        lang: '" . config('i18n.lang') . "',
                        getFileCallback:function(file){
                            $('.fancybox-close').click();
                            $.markItUp({replaceWith:\"+file.url+'\"});
                        }
                }).elfinder('instance');
        })", 'after');
        return '<div id="elfinder"></div>';
    }

}