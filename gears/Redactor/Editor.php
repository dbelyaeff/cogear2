<?php

/**
 * Редактор Redactor (масло маслянное)
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Redactor_Editor extends Wysiwyg_Abstract {

    /**
     *  Загрузка скриптов
     */
    public function load() {
        $this->toolbar = Core_ArrayObject::transform($this->toolbar);
        $folder = cogear()->redactor->folder . DS . 'redactor' . DS;
        $options = new Core_ArrayObject();
        event('redactor.options', $options);
        $options->lang = config('i18n.lang', 'ru');
        $options->shortcuts = TRUE;
        $options->minHeight = 300;
        $options->buttons = array('bold', 'italic', 'deleted', '|','formatting','|',
'unorderedlist', 'orderedlist', 'outdent', 'indent', '|','table', 'link', 'image','|',
'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule','|','html');
//        $options->imageUpload = l('/redactor/upload/');
        $options->fixed = TRUE;
        $options->observeImages = TRUE;
        $options->convertLinks = TRUE;
        role() == 1 && $options->fixedTop = 40;
        js($folder.$options->lang.'.js','after');
//        $this->toolbar->markupSet->uasort('Core_ArrayObject::sortByOrder');
//            $(document).ready(function(){
        css($folder . 'redactor.css');
        js($folder . 'redactor.min.js', 'after');
        inline_js("$('[name=$this->name]').redactor(" . $options->toJSON() . ")", 'after');
    }

}
