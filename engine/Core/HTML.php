<?php

/**
 * HTML support
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
final class HTML {

    public static $default_attributes = array('class', 'id', 'rel','data-id','data-type','data-source','data-placeholder');
    public static $allowed_attributes = array(
        'a' => array('href', 'target','rel'),
        'img' => array('width', 'height', 'alt', 'title','src'),
        'form' => array('action', 'method', 'enctype'),
        'input' => array('type', 'value', 'disabled', 'checked', 'name','placeholder'),
        'select' => array('type', 'disabled', 'name','multiple'),
        'option' => array('selected','value'),
        'textarea' => array('rows', 'cols', 'name', 'disabled','placeholder'),
        'script' => array('src', 'type', 'charset'),
        'link' => array('media', 'type', 'rel', 'href'),
        'meta' => array('content', 'type'),
    );

    /**
     * Compile attributes into string
     *
     * @param array $options
     * @return string
     */
    public static function attr(array $options) {
        $result = '';
        foreach ($options as $key => $value) {
            $value && $result .= $key . '="' . addslashes($value) . '" ';
        }
        return $result ? ' ' . trim($result) : '';
    }

    /**
     * Encode special characters in a plain-text string for display as HTML
	 *
	 * @param string $text
	 *			The text to be checked or processed.
	 * @return
	 *			An HTML safe version of $text
	 */

	public static function check_plain($text) {
		return htmlspecialchars($text, ENT_QUOTES);
	}

    /**
     * Filter attributes for defined tag
     *
     * @param string $tag
     * @param array $attributes
     */
    public static function filterAttributes($tag, $attributes) {
        $allowed_attributes = isset(self::$allowed_attributes[$tag]) ? array_merge(self::$default_attributes, self::$allowed_attributes[$tag]) : self::$default_attributes;
        $result = array();
        foreach ($attributes as $key => $value) {
            if (in_array($key, $allowed_attributes)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Render simple closed tag
     *
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public static function tag($name, $attributes) {
        $attributes = self::filterAttributes($name, $attributes);
        return '<' . $name . self::attr($attributes) . '/>';
    }

    /**
     * Open paired tag
     *
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public static function open_tag($name, $attributes) {
        $attributes = self::filterAttributes($name, $attributes);
        return '<' . $name . self::attr($attributes) . '>';
    }

    /**
     * Close paired tag
     *
     * @param string $name
     * @return string
     */
    public static function close_tag($name) {
        return '</' . $name . '>';
    }

    /**
     * Render paired tag
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return string
     */
    public static function paired_tag($name, $content, $attributes = array()) {
        return self::open_tag($name, $attributes) . $content . self::close_tag($name);
    }

    /**
     * Render script tag
     *
     * @param string $path
     * @param array $attributes
     * @param boolean   $inline
     * @return string
     */
    public static function script($path, $attributes = array(),$inline = NULL) {
        if(!$inline){
            $attributes['src'] = $path;
            $script = '';
        }
        else {
            unset($attributes['src']);
            $script = $path;
        }
        isset($attributes['charset']) OR $attributes['charset'] = 'utf-8';
        isset($attributes['type']) OR $attributes['type'] = 'text/javascript';
        return self::paired_tag('script', $script, $attributes);
    }

    /**
     * Render style tag
     *
     * @param string $path
     * @param array $attributes
     * @return string
     */
    public static function style($path, $attributes = array()) {
        $attributes['href'] = $path;
        isset($attributes['media']) OR $attributes['media'] = 'screen';
        isset($attributes['type']) OR $attributes['type'] = 'text/css';
        isset($attributes['rel']) OR $attributes['rel'] = 'stylesheet';
        return self::tag('link', $attributes);
    }

    /**
     * Render input tag
     *
     * @param   string  $type
     * @return  string
     */
    public static function input($attributes = array()) {
        return self::tag('input', $attributes);
    }

    /**
     * Render a tag
     *
     * @param string $href
     * @param string $content
     * @param array $attributes
     * @return string
     */
    public static function a($href, $content='', $attributes = array()) {
        $attributes['href'] = $href;
        return self::paired_tag('a', $content, $attributes);
    }

    /**
     * Render image
     *
     * @param string $src
     * @param string $alt
     * @param array $attributes
     * @return type
     */
    public static function img($src, $alt='', $attributes=array()) {
        $attributes['src'] = $src;
        $attributes['alt'] = $alt;
        return self::tag('img', $attributes);
    }

}