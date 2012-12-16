<?php

/**
 * Абстрактный класс работы с изображениями
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
abstract class Image_Driver_Abstract extends Options {

    /**
     * Информация об изображении
     *
     * @var Core_ArrayObject
     */
    protected $info = array(
        'file' => '',
    );

    /**
     * Ресурс источника изображения
     *
     * @var Resource
     */
    protected $source;

    /**
     * Ресурс преобразованного изображения
     *
     * @var Resource
     */
    protected $target;

    /**
     * Конструктор
     *
     * @param type $file
     * @param type $options
     */
    public function __construct($file, $options = array()) {
        parent::__construct($options);
        $this->info = new Core_ArrayObject($this->info);
        // Проверяем файл на существование
        if (file_exists($file)) {
            $this->info->file = $file;
            // Получаем параметры изображения
            if ($info = getimagesize($file)) {
                $this->info->width = $info[0];
                $this->info->height = $info[1];
                $this->info->type = $info[2];
                $this->info->mime = $info[3];
                $this->create();
            } else {
                throw new Exception(t('Указанный файл не является изображением: <b>%s</b>', $file));
            }
        } else {
            throw new Exception(t('Изображение не существует: <b>%s</b>', $file));
        }
    }

    /**
     * Умное определение размера
     *
     * @param string $size Выражение искомого размера
     * @param string $type Тип источника исходного размера width или height
     * @param   int  $prec Точность вычисления в знаках после запятой
     * @return  int
     */
    public function smartSize($size, $type = 'width', $prec = 0) {
        $source = $type == 'width' ? $this->info->width : $this->info->height;
        if (preg_match('#(\d+)%#', $size, $matches)) {
            $calc = round($source * ($matches[1] / 100), $prec);
            $size = str_replace($matches[0], $calc, $size);
        }
        if (preg_match('#([a-z]+)#', $size, $matches)) {
            switch ($matches[1]) {
                case 'center':
                    $calc = round($source / 2, $prec);
                    break;
                case 'left':
                case 'top':
                    $calc = 0;
                    break;
                case 'right':
                case 'bottom':
                    $calc = $source;
                    break;
            }
            $size = str_replace($matches[0], $calc, $size);
        }
        eval("\$size = $size;");
        return $size;
    }

    /**
     * Возвращает источник изображения
     *
     * @return  resource
     */
    public function getSource(){
        return $this->source;
    }
    /**
     * Возвращает путь к файлу изображени
     *
     * @return  string
     */
    public function getFile(){
        return $this->info->file;
    }
    /**
     * Создает изображение
     */
    abstract public function create();

    /**
     * Уничтожает изображение
     */
    abstract public function destroy();

    /**
     * Изменяет масштаб изображения
     */
    abstract public function resize($width, $height, $fit = 'inside', $scale = 'any');

    /**
     * Производит обрезку изображения
     */
    abstract public function crop($x, $y, $width, $height);

    /**
     * Производит слияние изображений
     */
    abstract public function merge(Image $image, $x, $y);

    /**
     * Сохраняет изображение
     */
    abstract public function save($file = NULL, $options = array());

    /**
     * Выводит изображение
     */
    abstract public function output($format, $options);
}