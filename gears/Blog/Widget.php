<?php

/**
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Blog_Widget extends Widgets_Widget {

    public $options = array(
        'class' => 'well blog-widget',
        'limit' => 5,
        'render' => 'sidebar',
        'order' => 1,
        'cache_ttl' => 3600,
    );
    protected $template = 'Widgets.widget';

    /**
     * Render
     */
    public function render() {
        // @todo Find a bug with db to avoid the next stoke
        cogear()->db->clear(); // Show be deleted
        $blogs = blog();
        $blogs->order('rating', 'desc');
        $blogs->limit($this->options->limit);
        if ($result = $blogs->findAll()) {
            $tpl = new Template('Blog/templates/widget');
            $tpl->blogs = $result;
            $this->code = $tpl->render();
        }
        return parent::render();
    }

}