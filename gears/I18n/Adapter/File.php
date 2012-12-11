<?php
/**
 * File i18n adapter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         I18n

 */
class I18n_Adapter_File extends I18n_Adapter_Abstract {
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