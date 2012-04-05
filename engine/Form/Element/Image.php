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
class Form_Element_Image extends Form_Element_Abstract {

    protected $image;
    protected $resize;
    protected $crop;
    protected $sizecrop;
    protected $watermark;
    protected $thumbnails;
    protected $path;
    protected $maxsize;
    protected $min;
    protected $max;
    protected  $preset;
    protected $overwrite = TRUE;
    protected $rename;
    protected $allowed_types = array('jpg','gif','png');
    /**
     * Set value from request
     *
     * @return  mixed
     */
    public function result() {
        $image = new Upload_Image($this->name, $this->getAttributes(), $this->validators->findByValue('Required'));
        if ($result = $image->upload()) {
            $this->is_fetched = TRUE;
            $this->image = $image->getInfo();
            $this->value = $result;
        }
        else {
            $this->errors = $image->errors;
        }
        return $this->value;
    }

    /**
     * Render
     */
    public function render() {
        $this->getAttributes();
        $this->attributes->type = 'file';
        if ($this->value && $this->value = Url::link(Url::toUri(UPLOADS . $this->value, ROOT, FALSE))) {
            $tpl = new Template('Form.image');
            $tpl->assign($this->attributes);
            $tpl->value = $this->value;
            $tpl->image = $this->image;
            $this->code = $tpl->render();
        }
        return parent::render();
    }

    /**
     * Perform ajax handler
     * 
     * @param string $action
     * @param string $value 
     */
    public function ajaxCall($action, $value = NULL) {
        switch ($action) {
            case 'replace':
                $this->value = '';
                break;
        }
    }

}