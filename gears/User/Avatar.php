<?php

/**
 * User avatar
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		User
 * @subpackage
 * @version		$Id$
 */
class User_Avatar extends Object{

    protected $file;
    protected $user;

    /**
     * Constructor
     * 
     * @param string $file 
     */
    public function __construct($file = NULL) {
        $this->file = $file && file_exists(UPLOADS . DS . $file) ? $file : config('user.default_avatar', 'avatars/0/avatar.png');
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
        $file = UPLOADS.'/'.$this->file;
        return HTML::img(Url::toUri(image_preset($preset, $file)), $this->object->login, array('class' => 'avatar'));
    }

    /**
     * Render avatar uri
     */
    public function __toString() {
        return $this->render();
    }

}
