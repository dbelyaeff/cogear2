<?php

/**
 *  Класс загрузки изображения
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Image_Upload extends File_Upload {

    /**
     * Параметры
     *
     * @var array
     */
    protected $options = array(
        'allowed_types' => array('jpg','png','gif','ico'),
        'min' => array(
            'width' => 0,
            'height' => 0,
        ),
        'max' => array(
            'width' => 0,
            'height' => 0,
        ),
        'overwrite' => TRUE,
        'name' => 'image',
        'maxsize' => '100Kb',
        'path' => UPLOADS,
    );

    /**
     * Ширина изображения
     *
     * @var int
     */
    protected $width;

    /**
     * Высота изображения
     * @var int
     */
    protected $height;

    /**
     * Тип изображения
     *
     * IMAGETYPE_XXX
     *
     * @var string
     */
    protected $type;

    /**
     * MIME-тип
     *
     * @var string
     */
    protected $mime;

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        if ($this->options->preset && $preset = config('image.presets.' . $this->options->preset)) {
            $preset->options && $this->options->extend($preset->options);
        }
    }

    /**
     * Загрузка
     *
     * @return  array|Core_ArrayObject
     */
    public function upload() {
        if ($result = parent::upload()) {
            if (is_array($result)) {
                foreach ($result as $file) {
                    $this->postProcess($file);
                }
            } else {
                $this->postProcess($result);
            }
        }
        return $result;
    }

    /**
     * Постобработка
     *
     * @param  array|Core_ArrayObject
     */
    public function postProcess($file) {
        $image = new Image($file->path);
        if ($this->options->preset) {
            $preset = new Image_Preset($this->options->preset);
            if ($preset->load()) {
                $preset->image($image)->process();
            }
        }
        $image->save();
    }

    /**
     * Получение информаици об изображении
     *
     * @return array
     */
    public function getInfo($file = '') {
        $file OR $file = $this->file->path;
        $info = getimagesize($file);
        $this->width = $info[0];
        $this->height = $info[1];
        $this->type = $info[2];
        return new Core_ArrayObject(array(
                    'width' => $this->width,
                    'height' => $this->height,
                    'type' => $this->type,
                ));
    }

    /**
     * Проверка границ на максимум
     *
     * @param   int $width Максимальная ширина
     * @param   int $height Максимальная высота
     * @param   boolean $strict
     * @return  boolean
     */
    public function checkMax($width, $height, $strict = NULL) {
        if (($strict && $this->width > $width && $this->height > $height) OR
                ($this->width > $width OR $this->height > $height)) {
            $this->error(t('Максимальный размер изображения должен быть не более <b>%sx%s</b> пикселей.', $width, $height));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Проверка границ на минимум
     *
     * @param   int $width Максимальная ширина
     * @param   int $height Максимальная высота
     * @param   boolean $strict
     * @return  boolean
     */
    public function checkMin($width, $height, $strict = NULL) {
        if (($strict && $this->width < $width && $this->height < $height) OR
                ($this->width < $width OR $this->height < $height)) {
            $this->error(t('Минимальный размер изображения должен быть не менее <b>%sx%s</b> пикселов.', $width, $height));
            return FALSE;
        }
        return TRUE;
    }

}