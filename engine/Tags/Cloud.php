<?php

/**
 * Tags cloud
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Tags_Cloud extends Options {

    /**
     * Tags cloud
     */
    public function render(){
        event('tags.cloud',$this);
        $links = new Tags_Link();
        if($result = $links->findAll()){
            $counter = array();
            foreach($result as $link){
                if(!isset($counter[$link->tid])){
                    $counter[$link->tid] = 0;
                }
                $counter[$link->tid] += 1;
            }
            $tag = tag();
            $tags = $tag->findAll();
            $tpl = new Template('Tags.cloud');
            $tpl->tags = $tags;
            $tpl->counter = $counter;
            return $tpl->render();
        }
        return '';
    }
}