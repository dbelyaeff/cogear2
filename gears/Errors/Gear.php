<?php

/**
 * Шестеренка "Ошибки"
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Errors_Gear extends Gear {

    protected $hooks = array(
        '404' => 'notFound',
        'empty' => 'showEmpty',
    );

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL);
        set_error_handler(array($this, 'showRawError'));
    }

    /**
     * Инициализатор
     */
    public function init() {
        parent::init();
        set_error_handler(array($this, 'showError'));
    }

    /**
     * Показать ошибку
     *
     * @param string $text
     * @param string $title
     */
    public function show($text, $title = '') {
        error($text, $title = '');
    }

    /**
     * Отображение фатальной ошибки
     *
     * @param type $message
     */
    public function fatalError($message) {
        exit(template('Errors/templates/fatal', array('message' => $message))->render());
    }

    /**
     * Обработка "сырой" ошибки
     *
     * @param type $errno
     * @param type $error
     * @param type $file
     * @param type $line
     * @param type $context
     */
    public function showRawError($errno, $error, $file, $line, $context) {
        echo '<pre>';
        echo <<<HTML
<b>Error №:</b> $errno
<b>Title:</b> $error
<b>File:</b> $file
<b>Line:</b> $line
================= Context =================
HTML;
        var_dump($context);
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
        error(t('Ошибка в файле <b>%s</b> на строке <b>%d</b>: <p><i>%s</i>', $file, $line, $error, $context), t('Ошибка'));
    }

    /**
     * Not found
     */
    public function notFound() {
        $this->request();
        cogear()->response->header('Status', '404 ' . Response_Object::$codes[404]);
        $tpl = new Template('Errors/templates/404');
        $tpl->show();
    }

    /**
     * Not found
     */
    public function showEmpty() {
        $this->request();
        template('Errors/templates/empty')->show();
    }

}

function fatal_error($message) {
    cogear()->errors->fatalError($message);
}