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
    protected $order = -1000;
    protected $default_template = 'Notify.alert';
    protected $hooks = array(
        'done' => 'renderFlash',
    );
    
    /**
     * Show message
     *  
     * @param type $body
     * @param type $title
     * @param type $class 
     */
    public function showMessage($body, $title=NULL, $class=NULL, $region = 'content') {
        $tpl = new Template($this->default_template);
        $tpl->body = $body;
        $tpl->title = $title;
        $tpl->class = $class;
        append($region, $tpl->render());
    }
    /**
     * Show message
     *  
     * @param type $body
     * @param type $title
     * @param type $class 
     */
    public function flashMessage($body, $title=NULL, $class=NULL, $region = 'content') {
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
    public function renderFlash(){
        if($this->session->messages){
            foreach($this->session->messages as $message){
                $this->showMessage($message['body'],$message['title'],$message['class'],$message['region']);
            }
            $this->session->messages = array();
        }
    }
}

/**
 * Show notification "info"
 * 
 * @param string $body
 * @param string $title 
 * @param string  $content
 */
function info($body, $title = NULL, $region='content') {
    cogear()->notify->showMessage($body, $title, NULL, $region);
}

/**
 * Show notification "success"
 * 
 * @param string $body
 * @param string $title 
 * @param string  $content
 */
function success($body, $title = NULL, $region='content') {
    cogear()->notify->showMessage($body, $title, 'success', $region);
}

/**
 * Show notification "error"
 * 
 * @param string $body
 * @param string $title 
 * @param string  $content
 */
function error($body, $title = NULL, $region='content') {
    cogear()->notify->showMessage($body, $title, 'error', $region);
}
/**
 * Show flash  notification "info"
 * 
 * @param string $body
 * @param string $title 
 * @param string  $content
 */
function flash_info($body, $title = NULL, $region='content') {
    cogear()->notify->flashMessage($body, $title, NULL, $region);
}

/**
 * Show flash  notification "success"
 * 
 * @param string $body
 * @param string $title 
 * @param string  $content
 */
function flash_success($body, $title = NULL, $region='content') {
    cogear()->notify->flashMessage($body, $title, 'success', $region);
}

/**
 * Show flash  notification "error"
 * 
 * @param string $body
 * @param string $title 
 * @param string  $content
 */
function flash_error($body, $title = NULL, $region='content') {
    cogear()->notify->flashMessage($body, $title, 'error', $region);
}