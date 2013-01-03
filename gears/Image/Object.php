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
    public $options = array(
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
    public static function uploadPath(){
        $dir = user()->dir() . '/images/' . date('Y/m/d');
        is_dir($dir) OR File::mkdir($dir);
        return $dir;
    }
}