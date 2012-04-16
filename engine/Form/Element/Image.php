<?php

/**
 * Form image element
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Element_Image extends Form_Element_File {

    /**
     * Set value from request
     *
     * @return  mixed
     */
    public function result() {
        $image = new Image_Upload($this->prepareOptions());
        if ($result = $image->upload()) {
            $this->is_fetched = TRUE;
            $this->image = $image->getInfo();
            $this->value = $result;
        } else {
            $this->errors = $image->errors;
            $this->value = $this->options->value;
        }
        return $this->value;
    }

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        $this->options->type = 'file';
        $tpl = new Template('Form.image');
        $tpl->assign($this->options);
        $this->code = $tpl->render();
        return $this->code;
    }

}