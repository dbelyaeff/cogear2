<?php

/**
 * File lang adapter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Lang_Driver_File extends Lang_Driver_Abstract {

    protected $options = array(
        'path' => LANG,
        'file' => '',
    );

    /**
     * Load data
     */
    public function load($path = NULL) {
        $path OR $path = $this->getPath();
        if ($data = Config::read($path)) {
            $this->import($data);
        }
    }

    /**
     * Save data
     */
    public function save($path = NULL) {
        $path OR $path = $this->getPath();
        $this->ksort();
        Config::write($path, $this->export());
    }

}