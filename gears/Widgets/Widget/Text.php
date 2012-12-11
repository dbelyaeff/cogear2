<?php

/**
 * Text widget
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Widgets_Widget_Text extends Widgets_Widget {
    public $defaults = array(
        'content' => '',
        'class' => 'well',
    );
    /**
     * Render
     */
    public function render() {
        $this->code = $this->settings->content;
        return parent::render();
    }

    public static function info(){
        return array(
            'name' => t('HTML code','Widgets.Text'),
            'description' => t('Simply display HTML code','Widgets.Text'),
            'package' => 'Standart',
        );
    }
}