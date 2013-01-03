<?php

/**
 * Search Widget
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Search_Widget extends Widgets_Widget {

    protected $options = array(
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