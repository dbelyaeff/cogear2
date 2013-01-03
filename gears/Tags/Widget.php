<?php

/**
 * Tags widget
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Tags_Widget extends Widgets_Widget {

    protected $options = array(
        'class' => 'well tags-widget',
        'limit' => 50,
        'render' => 'sidebar',
        'order' => 10,
        'cache_ttl' => 3600,
    );

    /**
     * Render
     */
    public function render() {
        $cloud = new Tags_Cloud(array('limit'=>$this->options->limit));
        $this->code = template('Tags/templates/widget',array('cloud'=>$cloud->render()))->render();
        return parent::render();
    }

}