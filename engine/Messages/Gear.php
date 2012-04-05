<?php

/**
 * Messages gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          messages
 * @version		$Id$
 * 
 * @todo
 * 
 * Integrate with jGrowl.
 */
class Messages_Gear extends Gear {

    protected $name = 'Messages';
    protected $description = 'Handle with messages dialogs and windows.';
    protected $order = 100;
    protected $type = Gear::MODULE;
    protected $template = 'Messages.message';
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
        $tpl = new Template("Messages.notification_jgrowl");
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
        $tpl = new Template("Messages.message");
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
		$tpl = new Template("Messages.dialog_close");
		$tpl->title = $title;
        $tpl->content = $content;
        $tpl->class = $class;
		prepend('content', $tpl->render());
	}

	/*
	 * Show dialog window with confirmation
	 */
	public function show_dialog_confirmation($content = NULL, $title = NULL, $class='info') {
		$tpl = new Template("Messages.dialog_confirmation");
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
        $this->session->messages OR $this->session->messages = new Core_ArrayObject();
        $this->session->messages->append($data);
    }

    /**
     * Show flashed messages
     */
    public function finish() {
        if ($this->session->messages) {
            foreach ($this->session->messages as $offset => $data) {
                call_user_func_array(array($this, 'show'), $data);
            }
            $this->session->destroy('messages');
        }
        if (config('messages.type', 'plain') == 'pop') {
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
 * Show success messages dialog
 * 
 * @param string $content
 * @param string $title
 */
function success($content=NULL, $title=NULL) {
	$class='success';
	$content OR $content = t('Operation is successfully completed.');
    cogear()->messages->show_message($content, $title, $class);
}

/**
 * Show flash success messages dialog
 * 
 * @param string $content
 * @param string $title
 */
function flash_success($content=NULL, $title=NULL, $class='success') {
    $content OR $content = t('Operation is successfully completed.');
    $title OR $title = t('Success');
    cogear()->messages->flash($content, $title, $class);
}

/**
 * Show info messages dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function info($content=NULL, $title=NULL, $class='info') {
    $content OR $content = t('Please pay attetion to this notification.');
    cogear()->messages->show_message($content, $title, $class);
}

/**
 * Show flash info messages dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function flash_info($content=NULL, $title=NULL, $class='info') {
    $content OR $content = t('Please pay attetion to this notification.');
    $title OR $title = t('Info');
    cogear()->messages->flash($content, $title, $class);
}

/**
 * Show error messages dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function error($content=NULL, $title=NULL) {
	$class='error';
	$content OR $content = t('Operation failed.');
    cogear()->messages->show_message($content, $title, $class);
}

/**
 * Show flash error messages dialog
 * 
 * @param string $content
 * @param string $title
 * @param string $class 
 */
function flash_error($content=NULL, $title=NULL, $class='error') {
    $content OR $content = t('Operation failed.');
    $title OR $title = t('Failure');
    cogear()->messages->flash($content, $title, $class);
}

/**
 * Show dialog with button 'Close'
 *
 * @param string $title
 * @param string $content
 * @param string $class
 */
function dialog_close($title=NULL, $content=NULL, $class='info') {
	cogear()->messages->show_dialog_close($content, $title, $class);
}

/**
 * Show dialog with buttons 'Ok' and 'Cancel'
 *
 * @param string $title
 * @param string $content
 * @param string $class
 */
function dialog_confirmation($title=NULL, $content=NULL, $class='info') {
	cogear()->messages->show_dialog_confirmation($content, $title, $class);
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
    cogear()->messages->show_notification($content, $class);
}