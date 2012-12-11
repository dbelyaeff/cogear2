<?php

/**
 * Popup Window
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Popup_Window extends Object {

    public $options = array(
        'name' => '',
        'header' => '',
        'body' => '',
        'actions' => array(
        ),
        'settings' => array(
            'show' => false,
            'keyboard' => true,
            'backdrop' => true,
        ),
    );

    /**
     * Get name
     * @return type
     */
    public function id() {
        return 'modal-' . $this->name;
    }

    /**
     * Get modal scripts
     *
     * @return string
     */
    public function script() {
        $script = "$('#{$this->id}').modal(" . json_encode($this->settings) . ");";
        return $script;
    }

    /**
     * Render
     */
    public function render() {
        event('Popup.window', $this);
        event('Popup.window.' . $this->name, $this);
        $tpl = new Template('Popup/templates/window');
        $this->options->id = $this->id();
        $tpl->assign($this->options);

        inline_js($this->script(), 'after');
        return $tpl->render();
    }

}