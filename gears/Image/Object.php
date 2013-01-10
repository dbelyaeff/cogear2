<?php

/**
 * Класс изображения
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Image_Object extends Object {

    /**
     * Настройки
     *
     * @var array
     */
    protected $options = array(
        'driver' => 'Image_Driver_GD',
    );

    /**
     * Конструктор
     *
     * @param type $file
     * @param type $options
     */
    public function __construct($file, $options = NULL) {
        parent::__construct();
        $this->object(new $this->options->driver($file, $options));
    }

    /**
     * Генерирует путь для загрузки изображений
     */
    public static function uploadPath() {
        $dir = user()->getUploadPath() . '/images/' . date('Y/m/d');
        is_dir($dir) OR File::mkdir($dir);
        return $dir;
    }

    /**
     * Генерирует код для миниатюры
     *
     * @param string $file
     * @param string $preset
     */
    public static function getThumbCode($file, $preset = 'image.medium') {
        $file = UPLOADS . $file;
        $thumbnail = image_preset($preset, $file, TRUE);
        $preset = str_replace('.','-',$preset);
        return HTML::a(File::pathToUri($file),self::getCode($thumbnail),array('class'=>'preset '.$preset));
    }

    /**
     * Генерирует код для изображения
     *
     * @param string $file
     */
    public static function getCode($file) {
        $file = ROOT . DS . $file;
        $info = getimagesize($file);
        return '<img src="' . File::pathToUri($file) . '" ' . $info[3] . ' alt="">';
    }

}