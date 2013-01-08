<?php

/**
 * Лучшие пользователи widgets
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class User_Widgets_Онлайн extends Widgets_Widget {

    protected $options = array(
        'class' => 'well Онлайн-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 10,
        'cache_ttl' => 0,
    );

    /**
     * Конструктор
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
        if (cogear()->session->get('Онлайн') === NULL OR time() - cogear()->session->get('Онлайн') > config('widgets.Онлайн.period', 60)) {
            $Онлайн = new Db_ORM('Онлайн');
            $Онлайн->uid = user()->id;
            $Онлайн->user_agent = server('HTTP_USER_AGENT');
            $Онлайн->session_id = cogear()->session->get('session_id');
            $Онлайн->ip = cogear()->session->get('ip');
            $Онлайн->created_date = time();
            $Онлайн->insert();
            cogear()->session->set('Онлайн', time());
        }
    }

    /**
     * Hook user login
     */
    public function hookUserLogin($User){
        cogear()->session->remove('Онлайн');
    }

    /**
     * Render
     */
    public function render() {
        $Онлайн = new Db_ORM('Онлайн');
        $Онлайн->where('created_date',time() - config('widgets.Онлайн.period', 60),' < ');
        $Онлайн->delete();
        $data = Core_ArrayObject::transform(
                        array(
                            'counters' => array(
                                'users' => 0,
                                'Боты' => 0,
                                'Гости' => 0,
                                'all' => 0,
                            ),
                            'users' => array(),
                            'Боты' => array(),
                            'guest' => array(),
                        )
        );

        if ($result = $Онлайн->findAll()) {
            foreach ($result as $item) {
                if ($item->uid) {
                    if (!$data->users->offsetExists($item->uid)) {
                        $data->users->offsetSet($item->uid, user($item->uid));
                        $data->counters->users++;
                    }
                } else {
                    if (preg_match('#(yandex|google|rss|bot|rambler|pubsub|parser|spider|feed)#ism', $item->user_agent, $bot)) {
                        if ($data->Боты->offsetExists($bot[0])) {
                            continue;
                        }
                        $data->Боты->offsetSet($bot[0]);
                        $data->counters->Боты++;
                    }
                    else {
                        $data->counters->Гости++;
                    }
                }
            }
            $data->counters->all = $data->counters->users + $data->counters->Боты + $data->counters->Гости;
        }
        $tpl = new Template('User/templates/widgets/Онлайн');
        $tpl->data = $data;
        $this->code = $tpl->render();
        return parent::render();
    }

}