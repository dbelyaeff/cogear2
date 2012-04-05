<?php

/**
 * Image Thumb class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Image
 * @subpackage
 * @version		$Id$
 */
class Image_Thumb extends Image_Object {

    protected $thumb;
    protected $size;
    /**
     * Thumbs directory
     * 
     * const
     */
    const DIR = '.thumbs';
    
    /**
     * Constructor
     * 
     * @param string $file
     * @param string $size 
     */
    public function __construct($file, $size = NULL) {
        parent::__construct($file);
        $this->size($size);
    }
    
    /**
     * Set Thumb size
     * 
     * @param string $size 
     * @return mixed
     */
    public function size($size = NULL){
        $size && $this->size = $this->getSizeFromString($size);
        return $size ? $this->size : NULL;
    }

    /**
     * Get thumb by exact size
     *  
     * @param type $size 
     */
    public function get($size = NULL) {
        if (!file_exists($this->file))
            return NULL;
        $this->size($size);
        $this->thumb = $this->buildThumbPath();
        if (filemtime($this->file) > filemtime($thumb)) {
            $this->makeThumb();
        }
        return $this->thumb;
    }

    /**
     * Make thumb
     */
    protected function makeThumb() {
        $this->sizecrop($this->size)->save($this->thumb);
        return $this->thumb;
    }

    /**
     * Build path for thumb
     */
    public function buildThumbPath($size = NULL, $file = NULL) {
        $this->size($size);
        $file OR $file = $this->file;
        $dir = dirname($file);
        $filename = basename($file);
        $path = $dir . DS . self::DIR . DS . $this->size->width . 'x' . $this->size->height . DS . $filename;
        $this->sizecrop($this->size->toString('x'));
        return $path;
    }
    
    /**
     * Magic __toString method
     * 
     * return string
     */
    public function __toString(){
        return $this->get();
    }
}