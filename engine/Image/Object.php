<?php
/**
 * Image Manipulation class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Image
 * @subpackage
 * @version		$Id$
 */
class Image_Object extends Object {
    protected $file;
    /**
     * Constructor
     *
     * @param string $file
     */
    public function __construct($file) {
        $this->file = file_exists($file) ? $file : NULL;
        $driver = config('image.driver', 'Image_Adapter_GD');
        $this->attach(new $driver($this->file));
    }
    /**
     * Get image file
     *
     * @return string
     */
    public function getFile(){
        return $this->file;
    }
    /**
     * Get image info by path
     *
     * @param string $path
     * @return Core_ArrayObject
     */
    public static function getInfo($path = NULL) {
        $path OR $path = $this->file->path;
        if(!file_exists($path)) return NULL;
        $info = getimagesize($path);
        return new Core_ArrayObject(array(
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'attributes' => $info[3],
            'mime' => $info['mime'],
        ));
    }

}