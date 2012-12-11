<?php

/**
 * Abstract converter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
abstract class Converter_Adapter_Abstract extends Cogearable {

    /**
     * Конструктор
     */
    public function __construct() {
        // Autoload converter options
        if (!$this->options = $this->session->get('converter.options')) {
            $this->options = new Core_ArrayObject();
        }
    }

    /**
     * Convert users
     */
    abstract public function users(Ajax $ajax);

    /**
     * Convert private messages
     */
    abstract public function pm(Ajax $ajax);

    /**
     * Convert blogs
     */
    abstract public function blogs(Ajax $ajax);

    /**
     * Convert friends
     */
    abstract public function friends(Ajax $ajax);

    /**
     * Convert posts
     */
    abstract public function posts(Ajax $ajax);
    /**
     * Convert favorite posts
     */
    abstract public function fave(Ajax $ajax);

    /**
     * Convert comments
     */
    abstract public function comments(Ajax $ajax);

    /**
     * Convert pages
     */
    abstract public function pages(Ajax $ajax);
    /**
     * Get converter steps
     *
     * @return array
     */
    public function getSteps() {
        return array(
            'users' => t('Users','Converter.steps'),
            'pm' => t('Private messages','Converter.steps'),
            'blogs' => t('Blogs','Converter.steps'),
            'friends' => t('Friends','Converter.steps'),
            'posts' => t('Posts','Converter.steps'),
            'fave' => t('Favorite posts','Converter.steps'),
            'comments' => t('Comments','Converter.steps'),
            'pages' => t('Pages','Converter.steps'),
        );
    }

    /**
     * Save converter options
     *
     * @param type $options
     */
    public function save($options = array()) {
        $this->options->extend($options);
        $this->session->set('converter.options', $this->options);
    }

    /**
     * Clear
     */
    public function clear() {
        $this->session->remove('converter.options');
    }

}