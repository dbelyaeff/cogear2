<?php

/**
 * Syndicate gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Syndicate_Gear extends Gear {

    protected $name = 'Syndicate';
    protected $description = 'Provite syndication feed.';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'post.list' => 'hookFeed',
        'comments.list' => 'hookFeed',
        'theme.head.meta.after' => 'hookMeta',
        'response.send' => 'hookResponse',
    );
    protected $routes = array(
    );
    protected $access = array(
    );
    protected $code;

    /**
     * Show feed
     */
    public function hookFeed($items) {
        if ($this->input->get('rss') !== NULL) {
            $tpl = new Template('Syndicate.rss');
            $tpl->title = $this->meta->info->title->toString(config('meta.title.delimiter', ' &raquo; '));
            $tpl->link = l() . $this->router->getUri() . '?rss';
            $tpl->items = $items;
            $this->code = '<?xml version="1.0" encoding="utf-8"?>'."\n".$tpl->render();
        }
        else {
            hook('head',array($this,'hookMeta'));
        }
    }

    /**
     *
     */
    public function hookResponse($Response){
        if($this->code !== NULL){
            $Response->header('Content-type','application/rss+xml; charset = UTF-8');
            $Response->sendHeaders();
            exit($this->code);
        }
    }
    /**
     * Hook meta
     */
    public function hookMeta(){
        echo '<link type="application/rss+xml" rel="alternate" href="'.l(TRUE).'?rss">';
    }
}