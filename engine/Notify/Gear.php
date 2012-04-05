<?php

/**
 * Notify gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Notify
 * @version		$Id$
 * 
 * @todo
 * 
 * Integrate with jGrowl.
 */
class Notify_Gear extends Gear {

    protected $name = 'Notify';
    protected $description = 'Handle with Notify dialogs and windows.';
    protected $order = 100;
    protected $type = Gear::MODULE;
    protected $template = 'Notify.message';
    protected $version = '0.1';
    const INFO = 0;
    const DIALOG = 1;
    const AJAX = 2;
    /**
     * Init
     */
    public function init() {
        parent::init();
        hook('done', array($this, 'finish'));
    }

    /**
     * Show notification
     * 
     * @param string $content
     * @param string $title
     * @param string $class
     * @param int $type
     */
    public function show_notification($content = NULL, $class = 'info', $type = NULL) {
        $tpl = new Template("Notify.notification_jgrowl");
        $tpl->content = $content;
        $tpl->class = $class;
	    $tpl->type = $type ? $type : self::INFO;
//	    if($class=="error") $tpl->sticky = 'true';
        prepend('content', $tpl->render());
    }

	/**
     * Show message
     *
     * @param string $content
     * @param string $title
     * @param string $class
     * @param int $type
     */
    public function show_message($content = NULL, $title = NULL, $class = 'info', $type = NULL) {
        $tpl = new Template("Notify.message");
        $tpl->title = $title;
        $tpl->content = $content;
        $tpl->class = $class;
	    $tpl->type = $type ? $type : self::INFO;
        prepend('content', $tpl->render());
    }

	/*
	 * Show dialog window
	 */
	public function show_dialog_close($content = NULL, $title = NULL, $class='info') {
		$tpl = new Template("Notify.dialog_close");
		$tpl->title = $title;
        $tpl->content = $content;
        $tpl->class = $class;
		prepend('content', $tpl->render());
	}

	/*
	 * Show dialog window with confirmation
	 */
	public function show_dialog_confirmation($content = NULL, $title = NULL, $class='info') {
		$tpl = new Template("Notify.dialog_confirmation");
		$tpl->title = $title;
        $tpl->content = $content;
        $tpl->class = $class;
		prepend('content', $tpl->render());
	}

    /**
     * Set flash message
     * 
     * @param string $content
     * @param string $title
     * @param string $class
     * @param int $type 
     */
    public function flash($content = NULL, $title = NULL, $class = 'info', $type = NULL) {
        $data = func_get_args();
        $this->session->Notify OR $this->session->Notify = new Core_ArrayObject();
        $this->session->Notify->append($data);
    }

    /**
     * Show flashed Notify
     */
    public function finish() {
        if ($this->session->Notify) {
            foreach ($this->session->Notify as $offset => $data) {
                call_user_func_array(array($this, 'show'), $data);
            }
            $this->session->destroy('Notify');
        }
        if (config('Notify.type', 'plain') == 'pop') {
            inline_js('$(document).ready(function(){$(".msg").message();})');
        }
//        inline_js("$(document).ready(function(){window.Messenger.render()});");
    }

	public function set_template($template)
	{
		if($template!="") $this->template = $template;
	}

}

/**
 * Show success Notify dialog
 * 
 * @param string $content
 * @param string $title
 */
function success($content=NULL, $title=NULL) {
	$class='success';
	$content OR $content = t('Operation is successfully completed.');
    cogear()->Notify->show_message($content, $title, $class);
}

/**
 * Show flash success Notify dialog
 * 
 * @param string $content
 * @param string $title
 */
function flash_success($content=NULL, $title=NULL, $class='success') {
    $content OR $content = t('Operation is successfully completed.');
    $title OR $title = t('Success');
    cogear()->Notify->flash($content, $title, $class);
}

/**
 * Show info Notify dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function info($content=NULL, $title=NULL, $class='info') {
    $content OR $content = t('Please pay attetion to this notification.');
    cogear()->Notify->show_message($content, $title, $class);
}

/**
 * Show flash info Notify dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function flash_info($content=NULL, $title=NULL, $class='info') {
    $content OR $content = t('Please pay attetion to this notification.');
    $title OR $title = t('Info');
    cogear()->Notify->flash($content, $title, $class);
}

/**
 * Show error Notify dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function error($content=NULL, $title=NULL) {
	$class='error';
	$content OR $content = t('Operation failed.');
    cogear()->Notify->show_message($content, $title, $class);
}

/**
 * Show flash error Notify dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function flash_error($content=NULL, $title=NULL, $class='error') {
    $content OR $content = t('Operation failed.');
    $title OR $title = t('Failure');
    cogear()->Notify->flash($content, $title, $class);
}

/**
 * Show dialog with button 'Close'
 *
 * @param string $title
 * @param string $content
 * @param string $class
 */
function dialog_close($title=NULL, $content=NULL, $class='info') {
	cogear()->Notify->show_dialog_close($content, $title, $class);
}

/**
 * Show dialog with buttons 'Ok' and 'Cancel'
 *
 * @param string $title
 * @param string $content
 * @param string $class
 */
function dialog_confirmation($title=NULL, $content=NULL, $class='info') {
	cogear()->Notify->show_dialog_confirmation($content, $title, $class);
}

/**
 * Show notification
 *
 * @param string $content
 * @param string $class
 * @return void
 */
function notify($content=NULL, $class="info") {
	$content OR $content = t('Please pay attetion to this notification.');
    cogear()->Notify->show_notification($content, $class);
}