<?php

/**
 *  Form Element Input Ajaxed
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class File_Element extends Form_Element_Abstract {

    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $cogear = cogear();
        $file = new File_Upload($this->prepareOptions());
        if ($value = $file->upload()) {
            $this->is_fetched = TRUE;
            $this->value = $value;
        } else {
            $this->errors = $file->errors;
            $this->value = $this->options->value;
        }
        return $this->value;
    }

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        $tpl = new Template('File/templates/element');
        $tpl->assign($this->options);
        $this->code = $tpl->render();
        $this->decorate();
        return $this->code;
    }
}