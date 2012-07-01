<?php

/**
 * Top users widgets
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_Widgets_Top extends Widgets_Widget {

    public $options = array(
        'class' => 'well user-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 2,
    );

    /**
     * Render
     */
    public function render() {
        $users = new User();
        $users->order('rating', 'desc');
        $users->limit($this->options->limit);
        if($result = $users->findAll()){
            $tpl = new Template('User.widgets/top');
            $tpl->users = $result;
            $this->code = $tpl->render();
        }
        return parent::render();
    }

}