<?php
/**
 *  Errors gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Errors_Gear extends Gear {
    protected $name = 'Errors gear';
    protected $description = 'Handle errors';
    protected $order =  1;
    /**
     * Init
     */
    public function init(){
        set_error_handler(array($this,'showError'));
        parent::init();
    }
    /**
     * Show error
     * @param string $text
     * @param string $title 
     */
    public function show($text,$title = ''){
        error($text,$title = '');
    }
    public function showError($errno,$error,$file,$line,$context){
        error(t('Error in file <b>%s</b> was found at line <b>%d</b>: <blockquote>%s</blockquote>','Errors',$file,$line,$error),t('Error'));
    }
    public function _404(){
        $this->response->header('Status', '404 '. Response::$codes[404]);
        $tpl = new Template('Errors.404');
        $tpl->show();
    }
}

function _404(){
    $cogear = getInstance();
    $cogear->router->exec(array($cogear->errors,'_404'));
}
