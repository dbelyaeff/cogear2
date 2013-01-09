<?php

/**
 * User avatar
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		User
 * @subpackage

 */
class User_Avatar extends Object{

    protected $file;
    protected $user;

    /**
     * Конструктор
     *
     * @param string $file
     */
    public function __construct($file = NULL) {
        $this->file = $file && file_exists(UPLOADS . DS . $file) ? $file : config('user.avatar.default', 'avatars/0/avatar.jpg');
    }

    /**
     * Get avatar file
     *
     * @return  string
     */
    public function getFile(){
        return UPLOADS.DS.$this->file;
    }
    /**
     * Render avatar
     *
     * @param string $file
     */
    public function render($preset = 'avatar.small') {
        return HTML::img(Url::toUri(image_preset($preset,$this->getFile())), $this->object()->login, array('class' => 'avatar'));
    }

    /**
     * Render avatar uri
     */
    public function __toString() {
        return $this->render();
    }

}
