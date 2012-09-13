<?php

/**
 * Notifications gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          messages
 * @version		$Id$
 *
 */
class Notify_Gear extends Gear {

    protected $name = 'Notifications';
    protected $description = 'Handle with messages dialogs and windows.';
    protected $order = -999;
    protected $template = 'Notify.alert';
    protected $hooks = array(
        'ignite' => 'renderFlash',
    );
    protected $is_core = TRUE;

    /**
     * Show message
     *
     * @param type $body
     * @param type $title
     * @param type $class
     * @param type $region
     */
    public function showMessage($body, $title = NULL, $class = NULL, $region = 'info') {
        if ($region == 'growl') {
            $this->template = 'Notify.growl';
            $region = 'after';
        }
        $tpl = new Template($this->template);
        $tpl->body = $body;
        $tpl->title = $title;
        $tpl->class = $class;
        $output = $tpl->render();
        return $region ? append($region, $output) : $output;
    }

    /**
     * Show message
     *
     * @param type $body
     * @param type $title
     * @param type $class
     */
    public function flashMessage($body, $title = NULL, $class = NULL, $region = 'before') {
        $this->session->messages OR $this->session->messages = new Core_ArrayObject();
        $this->session->messages->append(array(
            'body' => $body,
            'title' => $title,
            'class' => $class,
            'region' => $region,
        ));
    }

    /**
     * Render flash messages
     */
    public function renderFlash() {
        if ($this->session->messages) {
            foreach ($this->session->messages as $message) {
                $this->showMessage($message['body'], $message['title'], $message['class'], $message['region']);
            }
            $this->session->destroy('messages');
        }
    }

}

/**
 * Show notification "info"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 * @return
 */
function info($body, $title = NULL, $region = 'info') {
    return cogear()->notify->showMessage($body, $title, 'alert-info', $region);
}

/**
 * Show notification "warning"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 * @return
 */
function warning($body, $title = NULL, $region = 'info') {
    return cogear()->notify->showMessage($body, $title, 'alert-warning', $region);
}

/**
 * Show notification "success"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 * @return
 */
function success($body, $title = NULL, $region = 'info') {
    return cogear()->notify->showMessage($body, $title, 'alert-success', $region);
}

/**
 * Show notification "error"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 */
function error($body, $title = NULL, $region = 'info') {
    return cogear()->notify->showMessage($body, $title, 'alert-error', $region);
}

/**
 * Show flash  notification "info"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 */
function flash_info($body, $title = NULL, $region = 'info') {
    cogear()->notify->flashMessage($body, $title, NULL, $region);
}

/**
 * Show flash  notification "warning"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 */
function flash_warning($body, $title = NULL, $region = 'info') {
    cogear()->notify->flashMessage($body, $title, NULL, $region);
}

/**
 * Show flash  notification "success"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 */
function flash_success($body, $title = NULL, $region = 'info') {
    cogear()->notify->flashMessage($body, $title, 'alert-success', $region);
}

/**
 * Show flash  notification "error"
 *
 * @param string $body
 * @param string $title
 * @param string  $info
 */
function flash_error($body, $title = NULL, $region = 'info') {
    cogear()->notify->flashMessage($body, $title, 'alert-error', $region);
}