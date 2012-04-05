<?php

/**
 *  Images hanlder
 *
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage  	Image
 * @version		$Id$
 */
class Upload_Image extends Upload_File {

    /**
     * Allowed image types
     * @var string
     */
    protected $allowed_types = 'jpg,png,gif,ico';
    /**
     * Image width
     * @var int
     */
    protected $width;
    /**
     * Image height
     * @var int
     */
    protected $height;
    /**
     * Image type
     *
     * IMAGETYPE_XXX
     *
     * @var string
     */
    protected $type;
    /**
     * Image mime
     * 
     * @var string
     */
    protected $mime;
    /**
     * Preset name
     * 
     * @var string
     */
    protected $preset;

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = array()) {
        isset($options['allowed_types']) OR $options['allowed_types'] = $this->allowed_types;
        parent::__construct($name, $options);
    }

    /**
     * Upload
     * 
     * @return  boolean
     */
    public function upload() {
        if($this->options->preset && $preset = config('image.presets.'.$this->options->preset)){
            $preset->options && $this->options->mix($preset->options);
        }
        if ($result = parent::upload()) {
            $this->getInfo();
            $image = new Image($this->file->path);
            if ($this->options->preset) {
                $preset = new Image_Preset($this->options->preset);
                if ($preset->load()) {
                    $preset->image($image)->process();
                }
            } else {
                // Resize
                $this->options->resize && $image->resize($this->options->resize);
                // Crop
                $this->options->crop && $image->crop($this->options->crop);
                // Size & Crop
                $this->options->sizecrop && $image->sizecrop($this->options->sizecrop);
                // Watermark
                $this->options->watermark && $image->watermark($this->options->watermark);
            }
            $image->save();
            return $result;
        }
        return FALSE;
    }

    /**
     * Process upload
     *
     * @return boolean|string
     */
    protected function processUpload() {
        $this->getInfo($this->file->tmp_name);
        if ($this->options->max && !$this->checkMax($this->options->max->width, $this->options->max->height)
                OR $this->options->min && !$this->checkMin($this->options->max->width, $this->options->max->height)) {
            return FALSE;
        }
        return parent::processUpload();
    }

    /**
     * Get info about uploaded image
     *
     * @return array
     */
    public function getInfo($file = '') {
        $file OR $file = $this->file->path;
        list($this->width, $this->height, $this->type) = new Core_ArrayObject(getimagesize($file));
        return new Core_ArrayObject(array(
            'width' => $this->width,
            'height' => $this->height,
            'type' => $this->type,
        ));
    }

    /**
     * Check image dimensions for maximum
     *
     * @param   int $width Max width
     * @param   int $height Max height
     * @param   boolean $strict
     * @return  boolean
     */
    public function checkMax($width, $height, $strict = NULL) {
        if (($strict && $this->width > $width && $this->height > $height) OR
                ($this->width > $width OR $this->height > $height)) {
            $this->errors[] = t('Maximum image dimensions are <b>%sx%s</b>pixels.', 'Image', $width, $height);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Check image dimensions for minimum
     *
     * @param   int $width Min width
     * @param   int $height Min height
     * @param   boolean $strict
     * @return  boolean
     */
    public function checkMin($width, $height, $strict = NULL) {
        if (($strict && $this->width < $width && $this->height < $height) OR
                ($this->width < $width OR $this->height < $height)) {
            $this->errors[] = t('Minimal image dimensions are <b>%sx%s</b>pixels.', 'Image', $width, $height);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Magic call method
     * 
     * @param string $name
     * @param array $arguments 
     * @return mixed
     */
    public function __call($name, $arguments) {
        return is_callable(array($this->adapter, $name)) ? call_user_func_array(array($this->adapter, $name), $arguments) : NULL;
    }

}
