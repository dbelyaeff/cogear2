<?php

/**
 * Comments widget
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Comments_Widget extends Widgets_Widget {

    public $options = array(
        'class' => 'well comments-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 3,
        'cache_ttl' => 600,
    );

    /**
     * Render
     */
    public function render() {
        $comments = new Comments();
        $comments->select('MAX(id) as id');
        $comments->group('post_id');
        $comments->limit($this->limit);
        $comments->order('id', 'ASC');
        $comments->where('published');
        $comments->order = FALSE;
        if ($result = $comments->findAll()) {
            $ids = array();
            foreach ($result as $item) {
                $ids[] = $item->id;
            }
            $comments = new Comments();
            $comments->order = FALSE;
            $comments->order('id', 'DESC');
            $comments->where_in('id', $ids);
            if ($result = $comments->findAll()) {
                $tpl = new Template('Comments/templates/widget');
                $tpl->comments = $result;
                $this->code = $tpl->render();
            }
        }
        return parent::render();
    }

}