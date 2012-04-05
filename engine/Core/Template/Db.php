<?php
/**
 * Database template
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Template_Db extends Template_Abstract {
    /**
     * Constructor
     *
     * @param string $name
     */
    public function  __construct($name) {
        $this->name = $name;
        $path = Gear::preparePath($name, 'templates') . EXT;
        $this->path = $path;
        $this->getTemplate();
    }

    /**
     * Get template
     *
     * @return  string
     */
    protected function getTemplate(){
        $cogear = getInstance();
        if(!$this->code = $cogear->cache->read('templates/'.$this->name)){
          if($template = $cogear->db->where('name',$this->name)->order('last_update','DESC')->get('templates')->row()){
              $cogear->cache->write('templates/'.$this->name,$this->code);
          }
          else {
              $this->code = file_get_contents($this->path);
              $cogear->db->insert('templates',array('name'=>$this->name,'code'=>$this->code,'last_update'=>date('Y-m-d H:i:s')));
              $cogear->cache->write('templates/'.$this->name,$this->code);
          }
        }
        return $this->code;
    }
}