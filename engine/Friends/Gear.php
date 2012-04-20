<?php

/**
 * Friends gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Friends_Gear extends Gear {

    protected $name = 'Friends';
    protected $description = 'Manage friends';
    // After User is loaded
    protected $order = 11;
    protected $hooks = array(
        'user.navbar' => 'hookUserNavbar',
        'user.profile.fields' => 'hookUserProfile',
    );
    const BOTH = 3;
    const FROM = 2;
    const TO = 1;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        // This hook must be placed earlier, because it deals with User init method which loads before Friends gear init
        hook('user.refresh', array($this, 'clear'));
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        NULL !== $this->session->get('friends') OR $this->setFriends();
    }

    /**
     * Menu hook
     *
     * @param   string  $name
     * @param   object  $menu
     */
    public function menu($name, $menu) {
        d('Blog');
        switch ($name) {
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Friends') . ' (' . $menu->object->posts . ')',
                    'link' => $this->getLink($menu->object),
                ));
                break;
        }
        d();
    }

    /**
     * Add friend/unfriend badge to userbar
     *
     * @param type $Navbar
     */
    public function hookUserNavbar($Navbar) {
        if (access('friends') && $Navbar->object->id != $this->user->id) {
            $status = $this->check($Navbar->object->id);
            $link = l('/friends/status/' . $Navbar->object->id . '/');
            switch ($status) {
                case 0:
                case 1:
                    $class = 'btn-success';
                    $word = 'Friend';
                    break;
                case 2:
                    $class = 'btn-warning';
                    $word = 'Unfriend';
                    break;
                case 3:
                    $class = 'btn-danger';
                    $word = 'Unfriend';
                    break;
            }
            $link = sprintf('<a href="%s" data-type="modal" data-source="%s" class="btn btn-mini %s">%s</a>', $link, 'form-friends-status', $class, t($word, 'Friends'));
            $Navbar->append($link);
        }
    }

    /**
     * Add friends list to user profile
     *
     * @param type $Profile
     */
    public function hookUserProfile($Profile) {
        $user = $Profile->object;
        $friends = $this->getFriends($user->id);
        $both = new Core_ArrayObject();
        $one = new Core_ArrayObject();
        foreach ($friends as $id => $status) {
            if ($status == self::BOTH) {
                $user = new User();
                $user->id = $id;
                if ($user->find()) {
                    $both->append($user->getListView());
                }
            }
            if ($status == self::TO) {
                $user = new User();
                $user->id = $id;
                if ($user->find()) {
                    $one->append($user->getListView());
                }
            }
        }
        $both->count() && $Profile->append(array(
                    'label' => t('Friends', 'Friends.profile'),
                    'value' => implode(' ,', $both->toArray()),
                ));
        $one->count() && $Profile->append(array(
                    'label' => t('Subscribers', 'Friends.profile'),
                    'value' => implode(' ,', $one->toArray()),
                ));
    }

    /**
     * Set user friends
     */
    public function setFriends() {
        $this->session->set('friends', $this->getFriends());
    }

    /**
     * Get user friends
     *
     * @param int $uid
     * @reutnr  array
     */
    public function getFriends($uid = 0) {
        $uid OR $uid = $this->user->id && $self = TRUE;
        $friends = new Friends_Object();
        $friends->where('f', $uid)->or_where('t', $uid);
        if ($result = $friends->findAll()) {
            $friends = array();
            $fcount = 0;
            $scount = 0;
            foreach ($result as $item) {
                if ($item->f == $uid) {
                    if (isset($friends[$item->t])) {
                        $friends[$item->t] = self::BOTH;
                    } else {
                        $friends[$item->t] = self::FROM;
                    }
                }
                if ($item->t == $uid) {
                    if (isset($friends[$item->f])) {
                        $friends[$item->f] = self::BOTH;
                    } else {
                        $friends[$item->f] = self::TO;
                    }
                }
            }
            foreach ($friends as $status) {
                switch ($status) {
                    case 2:
                        $scount++;
                        break;
                    case 3:
                        $fcount++;
                        break;
                }
            }
            if (isset($self)) {
                $user = $this->user->adapter ? $this->user->adapter : new User();
                $user->friends = $fcount;
                $user->subscribers = $scount;
                $user->update(array('friends' => $user->friends, 'subscribers' => $user->subscribers));
            }
            return new Core_ArrayObject($friends);
        }
        return array();
    }

    /**
     * Reset current user friends
     */
    public function clear() {
        $this->session->delete('friends');
    }

    /**
     * Check user to be a friend
     *
     * @return int // 0 - no friendship, 1 - oneway, 2 - both
     */
    public function check($uid) {
        if ($friends = $this->session->get('friends')) {
            return isset($friends[$uid]) ? $friends[$uid] : 0;
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($action = '', $subaction = NULL) {

    }

    /**
     * Change status
     *
     * @param type $id
      status */
    public function status_action($id = 0) {
        $user = new User();
        $user->id = $id;
        if ($id && $user->find()) {
            $status = $this->check($id);
            $form = new Form('Friends.status');
            $form->init();
            switch ($status) {
                case 2:
                case 3:
                    $form->title->options->label = t('Remove %s from friends?', 'Friends', $user->getListView());
                    break;
                case 1:
                default:
                    $form->title->options->label = t('Add %s to friends?', 'Friends', $user->getListView());
            }
            if ($result = $form->result()) {
                if ($result->yes) {
                    $friends = new Friends_Object();
                    $friends->f = $this->user->id;
                    $friends->t = $user->id;
                    switch ($status) {
                        case 0:
                        case 1:
                            $friends->created_date = time();
                            $friends->save();
                            flash_success(t('You have a new friend.'));
                            break;
                        case 2:
                        case 3:
                            $friends->delete();
                            flash_error($message = t('Your friendship is over.'));
                    }
                }
                $this->clear();
                redirect($user->getLink());
            }
            $form->show();
        } else {
            return event('404');
        }
    }

}