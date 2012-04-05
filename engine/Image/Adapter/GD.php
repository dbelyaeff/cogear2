<?php

/**
 * Image Adapter Abstract
 * 
 * @todo    Fix watermark alpha blending on Denwer
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Image_Adapter_GD extends Image_Adapter_Abstract {

    protected $methods = array(
        'create' => 'imagecreatefrom',
        'render' => 'image',
    );

    /**
     * Set driver by image type
     */
    public function __construct($path) {
        parent::__construct($path);
        $this->methods = new Core_ArrayObject($this->methods);
        switch ($this->info->type) {
            case IMAGETYPE_JPEG:
                $this->source = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_GIF:
                $this->source = imagecreatefromgif($path);
                break;
            case IMAGETYPE_PNG:
                $this->source = imagecreatefrompng($path);
                imagealphablending($this->source, TRUE);
                imagesavealpha($this->source, TRUE);
                break;
            case IMAGETYPE_ICO:
                $this->source = imagecreatefromstring(Filesystem::read($path));
                break;
        }
    }

    /**
     * Create new Image
     * 
     * @param   int $width
     * @param   int $height
     */
    public function create($width, $height) {
        return imagecreatetruecolor($width, $height);
    }

    /**
     * Resize
     * 
     * @param string    $size 
     */
    public function resize($size) {
        $size = $this->prepare($size);
        imagecopyresized($this->destination, $this->source, 0, 0, 0, 0, $size->width, $size->height, $this->info->width, $this->info->height);
        $this->info->width = $size->width;
        $this->info->height = $size->height;
        $this->exchange();
        return $this;
    }

    /**
     * Crop
     * 
     * @param string $crop 
     * @param double    $x  If it's < 1, than it is a percent. 0.5 = 50%. When it's > 1, it's exact pixel.
     * @param double     $y Likely as $x.
     * @param boolean $maintain_ratio
     */
    public function crop($size, $x=0.5, $y=0.5, $maintain_ratio = NULL) {
        $maintain_ratio !== NULL && $this->options->maintain_ratio = $maintain_ratio;
        $size = $this->prepare($size);
        $from = new stdClass();
        $to = new stdClass();
        $from->x = $to->x = 0;
        $from->y = $to->y = 0;
        if ($this->info->width > $size->width) {
            $from->x = $x < 1 ? $this->info->width * $x - $size->width / 2 : $x;
            $to->x = $from->x + $size->width;
        }
        if ($this->info->height > $size->height) {
            $from->y = $y < 1 ? $this->info->height * $y - $size->height / 2 : $y;
            $to->y = $from->y + $size->height;
        }
        imagecopy($this->destination, $this->source, 0, 0, $from->x, $from->y, $size->width, $size->height);
        $this->exchange($size);
        return $this;
    }

    /**
     * Resize to fit size and than crop
     * 
     * @param string $size
     * @param float $x
     * @param float $y 
     */
    public function sizecrop($size, $x=0.5, $y=0.5) {
        $resize = $this->prepare($size);
        $side = $resize->width > $resize->height ? $resize->height : $resize->width;
        $this->resize($side . 'x' . $side);
        $this->crop($size, $x, $y, FALSE);
        return $this;
    }

    /**
     * Rotate image
     * 
     * @param type $angle
     */
    public function rotate($angle) {
        $this->prepare();
        $this->destination = imagerotate($this->source, $angle, -1);
        return $this;
    }

    /**
     * Watermark
     * 
     * You can place watermark at one of 9 zones
     * 
     * --------------------------
     * |    1   |   2   |   3   |
     * --------------------------
     * |    4   |   5   |   6   |
     * --------------------------
     * |    7   |   8   |   9   |
     * --------------------------
     * 
     * @param   mixed   $watermark
     * @param   int     $zone
     */
    public function watermark($watermark = NULL, $zone = 9, $opacity = 100) {
        $watermark && file_exists($watermark) OR $watermark = config('watermark', ENGINE . DS . 'Core.' . DS . 'images' . DS . 'watermark.png');
        $this->prepare();
        imagealphablending($this->destination, TRUE);
//        imagesavealpha($this->destination, TRUE);
        $watermark = new self($watermark);
        imagecopy($this->destination, $this->source, 0, 0, 0, 0, $this->info->width, $this->info->height);
        switch ($zone) {
            case 1:
                $top = 0;
                $left = 0;
                break;
            case 2:
                $top = 0;
                $left = ($this->info->width - $watermark->info->width) / 2;
                break;
            case 3:
                $top = 0;
                $left = ($this->info->width - $watermark->info->width);
                break;
            case 4:
                $top = ($this->info->height - $watermark->info->height) / 2;
                $left = 0;
                break;
            case 5:
                $top = ($this->info->height - $watermark->info->height) / 2;
                $left = ($this->info->width - $watermark->info->width) / 2;
                break;
            case 6:
                $top = ($this->info->height - $watermark->info->height) / 2;
                $left = ($this->info->width - $watermark->info->width);
                break;
            case 7:
                $top = ($this->info->height - $watermark->info->height);
                $left = 0;
                break;
            case 8:
                $top = ($this->info->height - $watermark->info->height);
                $left = ($this->info->width - $watermark->info->width) / 2;
                break;
            case 9:
            default:
                $top = ($this->info->height - $watermark->info->height);
                $left = ($this->info->width - $watermark->info->width);
        }
        imagecopymerge($this->destination, $watermark->source, $left, $top, 0, 0, $watermark->info->width, $watermark->info->height, $opacity);
        $watermark->clear();
        return $this;
    }

    /**
     * Save to file
     * 
     * @param string $file 
     */
    public function save($file = NULL) {
        $this->prepare();
        $path = $file ? $file : $this->path;
        Filesystem::makeDir(dirname($path));
        switch ($this->info->type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->source, $path, config('image.jpeg.quality', 75));
                break;
            case IMAGETYPE_GIF:
                imagegif($this->source, $path);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->source, $path, config('image.png.compression', 9));
                break;
            case IMAGETYPE_ICO:
                imagegd2($this->source, $path);
                break;
        }
        $this->clear();
    }

    /**
     * Render
     */
    public function render() {
        $this->prepare();
        header('Content-type: ' . $this->info->mime);
        switch ($this->info->type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->source, NULL, config('image.jpeg.quality', 75));
                break;
            case IMAGETYPE_GIF:
                imagegif($this->source, NULL);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->source, NULL, config('image.png.quality', 75));
                break;
            case IMAGETYPE_ICO:
                imagegd2($this->source, NULL);
                break;
        }
        $this->clear();
    }

    /**
     * Clean up
     */
    public function clear() {
        @imagedestroy($this->source);
        //@imagedestroy($this->destination);
    }

}