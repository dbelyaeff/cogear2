<?php

/**
 * Image Adapter Abstract
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
abstract class Image_Adapter_Abstract extends Options {

    /**
     * File path
     * 
     * @var string
     */
    protected $path;
    /**
     * 
     *  
     * @var type 
     */
    protected $info;
    /**
     * Source image
     * 
     * @var resource
     */
    protected $source;
    /**
     * Destination image
     * 
     * @var resource
     */
    protected $destination;
    /**
     * Options
     * 
     * @var array
     */
    public $options = array(
        'maintain_ratio' => TRUE,
    );

    /**
     * Constructor
     * 
     * @param string $file 
     * @return  boolean
     */
    public function __construct($path) {
        $this->path = $path;
        $this->info();
        $this->options = new Core_ArrayObject($this->options);
    }

    /**
     * Update and return info about current image
     * 
     * @return object 
     */
    public function info() {
        return $this->info = Image::getInfo($this->path);
    }

    /**
     * Process size from string to array
     * 
     * @param string $size
     * @return array 
     */
    protected function getSize($size) {
        if (is_string($size)) {
            $size = explode('x', $size);
            if (file_exists($this->path)) {
                list($width, $height) = getimagesize($this->path);
            } else {
                $width = $size[0];
                isset($size[1]) && $height = $size[1];
            }
            if (sizeof($size) == 1) {
                $size[1] = $this->image->options->maintain_ratio === FALSE ? $size[0] : $height * $size[0] / $width;
            } elseif ($this->options->maintain_ratio) {
                $ratio = $this->info->width / $this->info->height;
                $size[1] = round($size[0] / $ratio);
            }
        }
        return new Core_ArrayObject(array('width' => $size[0], 'height' => $size[1]));
    }

    /**
     * Prepare to image manipulation
     *  
     * @param string $size 
     * @param boolean   $save
     * @return  object
     */
    protected function prepare($size = NULL) {
        if ($size) {
            $size = $this->getSize($size);
            $this->destination = $this->create($size->width, $size->height);
            return $size;
        }
        return NULL;
    }

    /**
     * Move proccessed image as new one
     */
    protected function exchange($size = NULL) {
        if ($size instanceof Core_ArrayObject) {
            $this->info->width = $size->width;
            $this->info->height = $size->height;
        }
        // If we have previous operation — save it result to source
        $this->destination && $this->source = $this->destination;
    }

    /**
     * Size
     * 
     * @param
     */
    public function getSizeFromString($size) {
        // Simple 200x200 or just 200
        if (preg_match('(\d+(x\d+)?)', $size)) {
            $size = explode('x', $size);
            if (sizeof($size) == 1) {
                $size[1] = $size[0];
            }
            return new Core_ArrayObject(array('width' => $size[0], 'height' => $size[1]));
        }
        // Preset usage
        // Example: small_24x24 or any name you like with a-z_- characters in name
        elseif(preg_match('([\w_-]+)', $size)){
            $preset = new Image_Preset($size);
            if($preset->load()){
                return $this->getSizeFromString($preset->size);
            }
        }
        return NULL;
    }

    abstract public function create($width, $height);

    abstract public function resize($size);

    abstract public function crop($size, $x = 0.5, $y = 0.5);

    abstract public function rotate($angle);

    abstract public function watermark($watermark = NULL);

    abstract public function save($path = NULL);

    abstract public function clear();
}