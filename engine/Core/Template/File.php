<?php

/**
 * Template
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Template_File extends Template_Abstract {
    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct($name);
        $path = Gear::preparePath($this->name, 'templates') . EXT;
	        if(file_exists($path)){
            $this->path = $path;
            //$this->code = file_get_contents($this->path);
            ini_get('short_open_tag') OR $this->code = str_replace('<?=','<?php echo',$this->code);
        }
        else {
            /**
             * There we have an option → render as normal error or with just simple exit.
             * If we try to render any Messages (success, notify or error template) we can be easliy catched by looping error throwing us into nowhere.
             * That's why we use such a hint there.
             */
            $message = t('Template <b>%s</b> is not found by path <u>%s</u>.','Errors',$this->name,$this->path);
            exit($message);
//            strpos($this->name, 'Messages') !== FALSE ? exit($message) : error($message);
        }
    }
}
