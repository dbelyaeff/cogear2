<?php

/**
 *  Image upload class
 *
 *
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * 	Image

 */
class Image_Upload extends File_Upload {

    public $options = array(
        'allowed_types' => 'jpg,png,gif,ico',
        'min' => array(
            'width' => 0,
            'height' => 0,
        ),
        'max' => array(
            'width' => 0,
            'height' => 0,
        ),
        'resize' => '',
        'crop' => '',
        'sizecrop' => '',
        'watermark' => '',
        'overwrite' => TRUE,
        'name' => 'image',
        'maxsize' => '100Kb',
        'path' => UPLOADS,
    );

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
     * Upload
     *
     * @return  boolean
     */
    public function upload() {
        if ($this->options->preset && $preset = config('image.presets.' . $this->options->preset)) {
            $preset->options && $this->options->extend($preset->options);
        }
        if ($result = parent::upload()) {
            if (is_array($result)) {
                foreach ($result as $file) {
                    $this->postProcess($file);
                }
            } else {
                $this->postProcess($file);
            }
        }
        return $result;
    }

    /**
     * Post process
     *
     * @param type $file
     */
    public function postProcess($file) {
//        $this->getInfo($file);
        $image = new Image($file->path);
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
    }

    /**
     * Process upload
     *
     * @return boolean|string
     */
    protected function process($file) {
        if ($file = parent::process($file)) {
            $this->getInfo($file->path);
            if ($this->options->max->width && $this->options->max->height && !$this->checkMax($this->options->max->width, $this->options->max->height)
                    OR $this->options->min->width && $this->options->min->height && !$this->checkMin($this->options->min->width, $this->options->min->height)) {
                @unlink($file->path);
                $this->uploaded = FALSE;
            }
        }
        return $file;
    }

    /**
     * Get info about uploaded image
     *
     * @return array
     */
    public function getInfo($file = '') {
        $file OR $file = $this->file->path;
        $info = getimagesize($file);
        $this->width = $info[0];
        $this->height = $info[1];
        $this->type = $info[2];
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

}
