<?php

/**
 * Image gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Image
 * @subpackage          
 * @version		$Id$
 */
class Image_Gear extends Gear {

    protected $name = 'Image';
    protected $description = 'Image processor.';
    protected $package = 'Images';

}

function image_preset($name, $path,$toUri = FALSE) {
    $preset = new Image_Preset($name);
    if ($preset->load()) {
        $image = $preset->image($path)->render();
        return $toUri ? Url::toUri($image) : $image;
    }
}