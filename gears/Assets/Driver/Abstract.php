<?php
/**
 * Драйвер для загрузки скриптов
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
abstract class Assets_Driver_Abstract extends Object {

    public $options = array(
        'glue' => TRUE,
        'render' => 'head',
    );
    /**
     * Конструкторв
     *
     * @param type $options
     */
    public function __construct($options = NULL) {
        parent::__construct($options);
        $this->options->render && hook($this->options->render,array($this,'output'));
    }
    /**
     * Загрузка директории во внутренне хранилище
     *
     * @param string $dir
     * @param string $ext
     */
    public function loadDir($dir,$ext = 'js'){
        if(isset($dir) && $files = glob($dir.DS.'*.'.$ext)){
            foreach($files as $file){
                $this->append($file);
            }
        }
    }

    abstract function output();
}