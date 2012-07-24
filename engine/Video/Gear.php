<?php

/**
 * Video gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Video_Gear extends Gear {

    protected $name = 'Video';
    protected $description = 'Integrate video services';
    protected $package = 'Video';
    protected $order = 0;
    protected $hooks = array(
        'markitup.toolbar' => 'hookMarkitup',
        'post.render' => 'hookPostRender',
        'chat.msg.render' => 'hookPostRender',
        'comment.render' => 'hookPostRender',
    );

    /**
     * Add video icon to editor
     *
     * @param type $toolbar
     */
    public function hookMarkitup($toolbar) {
        $toolbar->markupSet->offsetSet('video', array(
            'name' => 'Video',
            'key' => 'O',
            'replaceWith' => "\n<video>[![" . t('Link to video') . "]!]</video>\n",
            'className' => 'markItUpVideo',
        ));
    }

    /**
     * Hook post render
     *
     * @param type $post
     */
    public function hookPostRender($post) {
        preg_match_all('#(?:\[|<)video(?:\]|>)(.*?)(?:\[|<)\/video(?:\]|>)#', $post->body, $video);
        preg_match_all('#\[media\](.*?)\[\/media]#', $post->body, $media);
        preg_match_all('#httpv:\/\/([^\s]+?)#imsU', $post->body, $httpv);
        if (!count($video[1]) && !count($media[1]) && !count($httpv[1])) {
            return;
        }
        $matches[0] = array_merge_recursive($video[0],$media[0],$httpv[0]);
        $matches[1] = array_merge_recursive($video[1],$media[1],$httpv[1]);
        $width = NULL;
        $height = NULL;
        if($post instanceof Chat_Messages){
            $width = 300;
            $height = 225;
        }
        for($i = 0; $i < sizeof($matches[1]); $i++){
            $post->body = str_replace($matches[0][$i], Video_Embed::getCode($matches[1][$i],$width,$height),$post->body);
        }
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

}