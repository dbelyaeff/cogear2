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
    protected $order = 1;
    protected $is_core = TRUE;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);
        set_error_handler(array($this, 'showRawError'));
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        set_error_handler(array($this, 'showError'));
    }

    /**
     * Show error
     *
     * @param string $text
     * @param string $title
     */
    public function show($text, $title = '') {
        error($text, $title = '');
    }

    public function showRawError($errno, $error, $file, $line, $context) {
        echo '<pre>';
        echo <<<HTML
<b>Error №:</b> $errno
<b>Title:</b> $error
<b>File:</b> $file
<b>Line:</b> $line
================= Context =================
HTML;
        debug($context);
        echo '
================= Context =================</pre>';
    }

    /**
     * Show error
     *
     * @param type $errno
     * @param type $error
     * @param type $file
     * @param type $line
     * @param type $context
     */
    public function showError($errno, $error, $file, $line, $context) {
        error(t('Error in file <b>%s</b> was found at line <b>%d</b>: <p><i>%s</i>', 'Errors', $file, $line, $error), t('Error'));
    }

    public function _404() {
        $this->response->header('Status', '404 ' . Response::$codes[404]);
        $tpl = new Template('Errors/templates/404');
        $tpl->show();
    }

}

function _404() {
    $cogear = getInstance();
    $cogear->router->exec(array($cogear->errors, '_404'));
}
