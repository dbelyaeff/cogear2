<?php

/**
 * Url class
 *
 * Bulid links.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Url {
    const SECURE = 's';

    /**
     * Build link
     *
     * @param	string	$url
     * @param	boolean	$absolute_flag
     * @param	string	$protocol
     * @return	string
     */
    public static function link($url='', $absolute_flag = FALSE, $protocol = 'http') {
        $link = '';
        $cogear = getInstance();
        if (!$url) {
            return $protocol . '://' . SITE_URL . '/';
        } else if (TRUE === $url) {
            return l() . cogear()->router->getUri();
        }
        $url = parse_url($url);

        if ($absolute_flag) {
            $link .= $protocol . '://';
            $link .= config('site.url');
        } elseif (defined('FOLDER')) {
            $link .= '/' . FOLDER;
        }
        isset($url['host']) && $link = $protocol . '://' . $url['host'];
        isset($url['path']) && $link .= '/' . ltrim($url['path'], '/');
        isset($url['query']) && $link .= '?' . $url['query'];
        isset($url['fragment']) && $link .= '#' . $url['fragment'];
        event('link', $link);
        return $link;
    }

    /**
     * Secure linke
     *
     * @param string $url
     * @param boolean $absolute_flag
     * @param string $protocol
     * @return string
     */
    public static function slink($url='', $absolute_flag = FALSE, $protocol = 'http') {
        $link = self::link($url, $absolute_flag, $protocol);
        $link .= '?' . self::SECURE . '=' . cogear()->secure->salt();
        return $link;
    }

    /**
     * Standartize
     *
     * Transform text to url-compatible snippet
     *
     * @param   string  $text
     * @param   string  $separator
     * @param   int     $limit
     * @return  string
     */
    public static function name($text, $separator = '-', $limit = 40) {
        $cogear = getInstance();
        $text = $cogear->i18n->transliterate($text);
        $text = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $text);
        $text = preg_replace('#[\-]+#i', $separator, $text);
        $text = strtolower($text);

        if (strlen($text) > $limit) {
            $text = substr($text, 0, $limit);
            if (($temp_max = strrpos($text, '-')))
                $text = substr($text, 0, $temp_max);
        }

        return $text;
    }

    /**
     * Transform path to uri
     *
     * @param string $path
     * @param string $replace_path
     * @param boolean $link
     * @return string
     */
    public static function toUri($path, $replace_path = NULL, $link = TRUE) {
        $replace_path OR $replace_path = ROOT;
        $path = str_replace(
                array($replace_path, DS), array('', '/'), $path);
        return $link ? self::link($path) : $path;
    }

    /**
     * Make link for gear
     *
     * @param string $gear
     * @param string $suffix
     * @return string
     */
    public static function gear($gear, $suffix = '/') {
        $cogear = getInstance();
        if (!$cogear->$gear)
            return self::link();
        return self::link($cogear->$gear->base . '/' . trim($suffix, '/'));
    }

    /**
     * Extend existins $_GET query
     *
     * @param array $data
     * @return  string
     */
    public static function extendQuery($data = array(),$value = NULL) {
        if(!is_array($data) && $value){
            $data = array($data=>$value);
        }
        return http_build_query(array_merge($_GET, $data));
    }

}

function l($url='', $absolute_flag = FALSE, $protocol = 'http') {
    return Url::link($url, $absolute_flag, $protocol);
}

function s($url='', $absolute_flag = FALSE, $protocol = 'http') {
    return Url::slink($url, $absolute_flag, $protocol);
}

function e($data = array(),$value = NULL){
    return '?'.Url::extendQuery($data,$value);
}
