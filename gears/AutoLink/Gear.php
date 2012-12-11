<?php

/**
 * Шестеренка автоматической обработки ссылок
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class AutoLink_Gear extends Gear {

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