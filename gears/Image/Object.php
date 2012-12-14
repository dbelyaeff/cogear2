<?php

/**
 * Класс изображения
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
include dirname(__FILE__) . DS . 'lib' . DS . 'phpThumb' . DS . 'ThumbLib.inc' . EXT;

class Image_Object extends Object {

    protected $file;

    /**
     * Конструктор
     *
     * @param type $file
     * @param type $options
     */
    public function __construct($file, $options = NULL) {
        parent::__construct($options);
        $this->object(PhpThumbFactory::create($this->file = $file));
    }

    /**
     * Возврат пути файла
     *
     * @return type
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Действие над изображением.
     *
     * Суть метода в том, чтобы ретранслировать действия на объект WideImage
     *
     * @param   string  $action
     * @param   mixed
     * .....
     *
     */
    public function action() {
        $args = func_get_args();
// Если у нас значения переданы не как ('200','200'), а в виде строк '200x200'
        if (is_string($args[1]) && preg_match('#[\d+]x[\d+]#', $args[1])) {
            $size = explode('x', $args[1]);
// Чтобы не делать array_slice просто по разному оперируем первой переменной — действием
            $action = $args[0];
            $args[0] = $size[0];
            $args[1] = $size[1];
        } else {
            $action = array_shift($args);
        }
        $callback = new Callback(array($this->object(), $action));
        try {
            $result = $callback->run($args);
        } catch (Exception $e) {

        }
        return $result;
    }

    /**
     * Сохранение изображения в файл
     *
     * @param type $destination
     */
    public function save($destination = NULL) {
        $destination OR $destination = $this->file;
        $this->object()->save($destination);
    }

}