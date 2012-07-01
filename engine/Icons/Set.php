<?php
/**
 * Icons set
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage  	Icons
 * @version		$Id$
 */
class Icons_Set extends Options{
    /**
     * Name
     *
     * @var string
     */
    protected $name;
    /**
     * Icons set path
     *
     * @var string
     */
    protected $path;
    /**
     * Icons extension
     *
     * png, gif
     *
     * @var string
     */
    protected $ext;
    /**
     * Icons size
     *
     * 16x16
     *
     * @var string
     */
    protected $size;
    /**
     * Icons
     */
    protected $icons;
    /**
     * Constructor
     *
     * @param string $path
     * @param string $ext
     * @param string $size
     */
    public function  __construct($path,$ext='png',$size='16x16'){
        $this->path = $path;
        $this->name = basename($path);
        //$this->icons = Config::read($this->path.DS.'icons'.EXT);
        $this->ext = $ext;
        $this->size = $size;
    }
    /**
     * Get icon uri
     *
     * @param string $name
     * @return string|boolean
     */
    public function get($name){
        $file = $this->path.DS.$name.'.'.$this->ext;
        if(file_exists($file)){
            return Url::toUri($file);
        }
        return NULL;
        //return $this->icons->$name ? Url::toUri($this->icons->$name) : NULL;
    }

}