<?php

/**
 * Top users widgets
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class User_Widgets_Top extends Widgets_Widget {

    public $options = array(
        'class' => 'well user-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 2,
        'cache' => TRUE,
        'cache_ttl' => 3600,
    );

    /**
     * Render
     */
    public function render() {
        $users = new User();
        $users->order('rating', 'desc');
        $users->limit($this->options->limit);
        if($result = $users->findAll()){
            $tpl = new Template('User/templates/widgets/top');
            $tpl->users = $result;
            $this->code = $tpl->render();
        }
        return parent::render();
    }

}