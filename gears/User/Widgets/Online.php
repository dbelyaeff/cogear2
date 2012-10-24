<?php

/**
 * Top users widgets
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_Widgets_Online extends Widgets_Widget {

    public $options = array(
        'class' => 'well online-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 10,
        'cache_ttl' => 0,
    );

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        hook('done', array($this, 'hookDone'));
        hook('userLogin', array($this, 'hookUserLogin'));
    }

    /**
     * Hook Done
     */
    public function hookDone() {
        if (cogear()->session->get('online') === NULL OR time() - cogear()->session->get('online') > config('widgets.online.period', 60)) {
            $online = new Db_ORM('online');
            $online->uid = user()->id;
            $online->user_agent = server('HTTP_USER_AGENT');
            $online->session_id = cogear()->session->get('session_id');
            $online->ip = cogear()->session->get('ip');
            $online->created_date = time();
            $online->insert();
            cogear()->session->set('online', time());
        }
    }

    /**
     * Hook user login
     */
    public function hookUserLogin($User){
        cogear()->session->remove('online');
    }

    /**
     * Render
     */
    public function render() {
        $online = new Db_ORM('online');
        $online->where('created_date',time() - config('widgets.online.period', 60),' < ');
        $online->delete();
        $data = Core_ArrayObject::transform(
                        array(
                            'counters' => array(
                                'users' => 0,
                                'bots' => 0,
                                'guests' => 0,
                                'all' => 0,
                            ),
                            'users' => array(),
                            'bots' => array(),
                            'guest' => array(),
                        )
        );

        if ($result = $online->findAll()) {
            foreach ($result as $item) {
                if ($item->uid) {
                    if (!$data->users->offsetExists($item->uid)) {
                        $data->users->offsetSet($item->uid, user($item->uid));
                        $data->counters->users++;
                    }
                } else {
                    if (preg_match('#(yandex|google|rss|bot|rambler|pubsub|parser|spider|feed)#ism', $item->user_agent, $bot)) {
                        if ($data->bots->offsetExists($bot[0])) {
                            continue;
                        }
                        $data->bots->offsetSet($bot[0]);
                        $data->counters->bots++;
                    }
                    else {
                        $data->counters->guests++;
                    }
                }
            }
            $data->counters->all = $data->counters->users + $data->counters->bots + $data->counters->guests;
        }
        $tpl = new Template('User/templates/widgets/online');
        $tpl->data = $data;
        $this->code = $tpl->render();
        return parent::render();
    }

}