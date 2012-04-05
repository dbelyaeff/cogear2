<?php
/**
 * File i18n adapter
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          I18n
 * @version		$Id$
 */
class I18n_Adapter_File extends I18n_Adapter_Abstract {
    public function __construct($options = array(), $place = NULL) {
        parent::__construct($options, $place);
        $this->options->path = SITE.DS.'lang';
    }
    /**
     * Load data
     */
    public function load(){
        if($data = Config::read($this->options->path.DS.$this->options->lang.EXT)){
            $this->import($data);
        }
    }
    /**
     * Save data
     */
    public function save(){
        if($this->update_flag){
            $this->ksort();
            Config::write($this->options->path.DS.$this->options->lang.EXT,$this->export());
        }
    }
}