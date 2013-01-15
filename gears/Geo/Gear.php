<?php

/**
 * Шестеренка, помогающая крутить Землю
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Geo_Gear extends Gear {

    protected $routes = array(
//        'geo/?' => 'index_action'
    );
    protected $access = array(
//        'index' => TRUE
    );

//    public function index_action() {
//        $countries = new Config($this->dir . DS . 'countries' . EXT);
//        $images = File::findByMask($this->dir . DS . 'img' . DS . 'flags', '#.+(\.png)$#');
//        $dir = $this->dir . DS . 'img' . DS .'16x16';
//        File::mkdir($dir);
//        foreach ($images as $image) {
//            $country = pathinfo($image,PATHINFO_FILENAME);
//            if($code = $countries->findByValue($country)){
//                copy($image,$this->dir . DS . 'img' . DS .'16x16'.DS.strtolower($code).'.png');
//            }
//        }
//    }

}

//function flag($county_code){
//    $file = cogear()->geo->dir.DS.'img'.DS.'flags'.DS.$county_code.'.png';
//    if(file_exists($file)){
//        return File::pathToUri($file);
//    }
//    return NULL;
//}