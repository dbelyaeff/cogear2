<?php

/**
 * AutoLink gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class AutoLink_Gear extends Gear {

    protected $name = 'AutoLink';
    protected $description = 'Replace url address with link';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'post.render' => 'hookRender',
        'chat.msg.render' => 'hookRender',
        'comment.render' => 'hookRender',
    );

    /**
     * Hook post render
     *
     * @param type $item
     */
    public function hookRender($item) {
        if(preg_match_all('#\s+(http|https|ftp)://([\w_-]+)\.([\w]{2,4})([^\s\<\>]*)\s+#i', $item->body, $matches)){
            foreach($matches[0] as $url){
                $item->body = str_replace($url,'<a href="'.$url.'">'.$url.'</a>',$item->body);
            }
        }
    }
}