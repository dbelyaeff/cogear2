<?php

/**
 * Элемент изображения для формы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 */
class Image_Element extends File_Element {

    protected $template = 'Image/templates/element';
    protected $image;

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->image = new Image_Upload($this->options);
        $this->options = $this->image->options;
        if (is_string($this->options->allowed_types)) {
            $this->options->allowed_types = explode(',', $this->options->allowed_types);
        }
    }

    /**
     * Сохраняет изображение
     *
     * @return  mixed
     */
    public function result() {
        if ($result = $this->image->upload()) {
            $this->is_fetched = TRUE;
            $this->image = $this->image->getInfo();
            $result = File::pathToUri($result->path, UPLOADS);
        } else {
            $this->errors = $this->image->getErrors();
            $this->notices = $this->image->getNotices();
        }
        if ($this->validate()) {
            if ($result) {
                return $result;
            }
            return $this->errors ? FALSE : NULL;
        }
        return FALSE;
    }

    /**
     * Вывод
     */
    public function render() {
        $this->options->type = 'file';
        return parent::render();
    }

}