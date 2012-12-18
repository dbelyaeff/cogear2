<?php

/**
 * Драйвер Memcache для сессии
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Session_Driver_Memcache extends Cache_Driver_Memcache {
    protected $name;
    /**
     * Session open
     *
     * @return boolean
     */
    public function open($save_path,$session_name){
        $this->name = $session_name;
        return TRUE;
    }
    /**
     * Destroy
     *
     * @param string  $id
     */
    public function destroy($id){
        $this->remove($id);
    }
    /**
     * Session close
     *
     * @return boolean
     */
    public function close(){
        return TRUE;
    }
    /**
     * Session garbage collector
     *
     * @param int $ttl
     */
    public function gc($ttl) {
       
    }

}