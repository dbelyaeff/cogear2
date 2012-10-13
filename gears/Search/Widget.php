<?php

/**
 * Search Widget
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Search_Widget extends Widgets_Widget {

    public $options = array(
        'class' => 'search-widget',
        'render' => 'sidebar',
        'order' => -1,
    );

    /**
     * Render
     */
    public function render() {
        $tpl = new Template('Search/templates/widget');
        $this->code = $tpl->render();
        return parent::render();
    }

}