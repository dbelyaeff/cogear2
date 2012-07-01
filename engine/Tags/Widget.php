<?php

/**
 * Tags widget
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Tags_Widget extends Widgets_Widget {

    public $options = array(
        'class' => 'well tags-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 10,
    );

    /**
     * Render
     */
    public function render() {
        $cloud = new Tags_Cloud();
        $this->code = template('Tags.widget',array('cloud'=>$cloud->render()))->render();
        return parent::render();
    }

}