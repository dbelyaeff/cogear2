<?php
/**
 * Embed video class
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Video_Embed  {
    protected static $services = array(
      'youtube' => array(
          'pattern' => '#youtube\.com/watch\?.*v=([^&]*)#',
          'code' => '<iframe width="%width%" height="%height%" src="http://www.youtube.com/embed/%snippet%?wmode=opaque" frameborder="0" allowfullscreen></iframe>,'
      )
    );

    /**
     * Parse url
     *
     * @param string $url
     */
    public static function getCode($url,$width = NULL, $height = NULL){
        foreach(self::$services as $name=>$config){
            if(preg_match($config['pattern'],$url,$matches)){
                return str_replace(
                        array('%width%','%height%','%snippet%'),
                        array($width ? $width : config('video.width', 720),$height ? $height : config('video.height',480),$matches[1]),
                        $config['code']);
            }
        }
    }
}
//