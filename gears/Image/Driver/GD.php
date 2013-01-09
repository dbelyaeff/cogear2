<?php

/**
 * Драйвер работы с изображениями библиотеке GD.
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Image_Driver_GD extends Image_Driver_Abstract {

    /**
     * Создает изображение
     */
    public function create() {
        switch ($this->info->type) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $this->source = imagecreatefromjpeg($this->info->file);
                break;
            case IMAGETYPE_PNG:
                $this->source = imagecreatefrompng($this->info->file);
                imagecolortransparent($this->source, imagecolorallocate($this->source, 0, 0, 0));
                imagealphablending($this->source, FALSE);
                imagesavealpha($this->source, TRUE);
                break;
            case IMAGETYPE_GIF:
                $this->source = imagecreatefromgif($this->info->file);
                imagecolortransparent($this->source, imagecolorallocate($this->source, 0, 0, 0));
                imagealphablending($this->source, FALSE);
                imagesavealpha($this->source, TRUE);
                break;
            case IMAGETYPE_ICO:
                $this->source = imagecreatefromwbmp($this->info->file);
                break;
        }
    }

    /**
     * Уничтожает изображение
     */
    public function destroy() {
        imagedestroy($this->source);
        is_resource($this->target) && imagedestroy($this->target);
    }

    /**
     *  Создает целевое изображение
     *
     * @param mixed $width
     * @param mixed $height
     */
    public function createTarget($width, $height) {
        $this->target = imagecreatetruecolor($width, $height);
        if ($this->info->type == IMAGETYPE_PNG OR IMAGETYPE_GIF == $this->info->type) {
            imagecolortransparent($this->target, imagecolorallocate($this->target, 0, 0, 0));
            imagealphablending($this->target, FALSE);
            imagesavealpha($this->target, TRUE);
        }
        return $this->target;
    }

    /**
     * Изменяет масштаб изображения
     *
     * @param   int|string $width   Ширина
     * @param   int|string $height  Высота
     * @param   string $fit   Тип масштабирования width, height, fit
     * @param   int|string $scale   Какие изображения масштабировать any, up, down
     * @return  object  Изображение
     */
    public function resize($width, $height, $fit = 'width', $scale = 'any') {
        $source_width = $this->smartSize($width, 'width');
        $source_height = $this->smartSize($height, 'height');
        $args = func_get_args();
        // Проверяем тип масштабирования
        switch ($fit) {
            // Если подравниваем по ширине, то приводим высоту к нужным пропорциям
            case 'width':
                $width = $source_width;
                $height = round(($source_width * $this->info->height) / $this->info->width);
                break;
            // Если подравниванием по высоте, то приводим ширину к нужным пропорциям
            case 'height':
                $width = round(($this->info->width * $source_height) / $this->info->height);
                $height = $source_height;
                break;
            case 'crop':
                if ($this->info->width > $this->info->height) {
                    $width = round(($this->info->width * $source_height) / $this->info->height);
                    $height = $source_height;
                }
                else {
                    $width = $source_width;
                    $height = round(($this->info->height * $source_width) / $this->info->width);
                }
                break;
            // По умолчанию просто растягиваем
            default:
            case 'fit':
        }
        // Проверяем будет ли производиться масштабирование
        switch ($scale) {
            // Если изображение больше указанных размеров, с ним ничего не происходит
            case 'up':
                if ($width < $this->info->width && $height < $this->info->height) {
                    return $this;
                }
                break;
            // Если изображение меньше указанных размеров, с ним ничего не происходит
            case 'down':
                if ($width > $this->info->width && $height > $this->info->height) {
                    return $this;
                }
                break;
            case 'any':
            default:
            // Ничего не делаем. Филоним
        }

        $this->target = $this->createTarget($width, $height);
        if (imagecopyresampled($this->target, $this->source, 0, 0, 0, 0, $width, $height, $this->info->width, $this->info->height)) {
            $this->source = $this->target;
            $this->info->width = $width;
            $this->info->height = $height;
            if('crop' == $fit){
                return $this->crop('center','center' ,$source_width,$source_height);
            }
        }
        return $this;
    }

    /**
     * Производит обрезку изображения
     *
     * @param   mixed   $x  Координата обрезки x
     * @param   mixed   $y  Координата обрезки y
     * @param   mixed   $width Ширина обрезки
     * @param   mixed   $height Высотка обрезки
     * @return  object  Изображение
     */
    public function crop($x, $y, $width, $height) {
        $x = $this->smartSize($x, 'width') - $width/2;
        $y = $this->smartSize($y, 'height') - $height/2;
        $width = $this->smartSize($width, 'width');
        $height = $this->smartSize($height, 'height');
        $this->target = $this->createTarget($width, $height);
        if (imagecopyresampled($this->target, $this->source, 0, 0, $x, $y, $width, $height, $width, $height)) {
            $this->source = $this->target;
            $this->info->width = $width;
            $this->info->height = $height;
        }
        return $this;
    }

    /**
     * Производит слияние изображений
     */
    public function merge(Image $image, $x, $y, $percent = 100) {
        $x = $this->smartSize($x, 'width');
        $y = $this->smartSize($y, 'height');
        if (imagecopymerge($this->source, $image->getSource(), $x, $y, 0, 0, $image->object()->image->width, $image->object()->image->height, $percent)) {

        }
        return $this;
    }

    /**
     * Сохраняет изображение
     */
    public function save($file = NULL, $options = array()) {
        $this->target OR $this->target = $this->source;
        if (strpos($file, '.') OR $file = $this->info->file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
        } else {
            $ext = strtolower($file);
            $file = NULL;
        }
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $options OR $options = 90;
                imagejpeg($this->target, $file, $options);
                break;
            case 'gif':
                imagecolortransparent($this->target, imagecolorallocatealpha($this->target, 0, 0, 0, 127));
                imagealphablending($this->target, FALSE);
                imagesavealpha($this->target, TRUE);
                imagegif($this->target, $file);
                break;
            case 'png':
                imagealphablending($this->target, FALSE);
                imagesavealpha($this->target, TRUE);
                if (is_numeric($options)) {
                    $quality = $options;
                    $filters = PNG_NO_FILTER;
                } else {
                    $quality = isset($options['quality']) ? $options['quality'] : 9;
                    $filters = isset($options['filters']) ? $options['filters'] : PNG_NO_FILTER;
                }
                imagepng($this->target, $file, $quality, $filters);
                break;
        }
        $this->destroy();
    }

    /**
     * Выводит изображение
     */
    public function output($format, $options) {
        $this->save($format, $options);
    }

}