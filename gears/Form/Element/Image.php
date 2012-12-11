<?php

/**
 * Form image element
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

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
            $this->value = File::pathToUri($result->path, UPLOADS);
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
        $tpl = new Template('Form/templates/image');
        $tpl->assign($this->options);
        $this->code = $tpl->render();
        $this->decorate();
        return $this->code;
    }

}