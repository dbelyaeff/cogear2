<?php

/**
 * Установщик
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Install_Gear extends Gear {

    /**
     * Init
     */
    public function init() {
        parent::init();
        if ($this->status() == Gears::ENABLED) {
            $this->router->bind(':index', array($this, 'index'), TRUE);
            if (!check_route('install', Router::STARTS)) {
                redirect(l('/install'));
            }
        }
    }

    /**
     * Initializing menu system
     *
     * @param type $name
     * @param type $menu
     */
    public function menu($name, $menu) {
        if ($name == 'install') {

        }
    }

    /**
     * Request
     */
    public function request() {
        parent::request();
        if (config('installed')) {
            redirect('/');
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index($action = '') {
        new Menu_Tabs(array(
                    'name' => 'install',
                    'render' => 'content',
                    'elements' => array(
                        array(
                            'label' => t('1. Start'),
                            'link' => '',
                            'active' => check_route('install', Router::ENDS),
                        ),
                        array(
                            'label' => t('2. Check'),
                            'link' => '',
                            'active' => check_route('check', Router::ENDS),
                        ),
                        array(
                            'label' => t('3. Settings'),
                            'link' => '',
                            'active' => check_route('site', Router::ENDS),
                        ),
                        array(
                            'label' => t('4. Finish'),
                            'link' => '',
                            'active' => check_route('finish', Router::ENDS),
                        ),
                    ),
                ));
        switch ($action) {
            case 'check':
                $tpl = new Template('Install/templates/check');
                $tpl->show();
                break;
            case 'site':
                append('content', '<p class="alert alert-info">' . t('Define basic settings for your site.', 'Install') . '</p>');
                $form = new Form('Install/forms/site');

                if ($result = $form->result()) {
                    $config = new Config(SITE . DS . 'site' . EXT);
                    $config->site->name = $result->sitename;
                    $config->key OR $config->key = md5(md5(time()) + time() + $config->site->name);
                    $config->database->dsn = $result->database;
                    $config->store(TRUE);
                    redirect('/install/finish');
                } else {
                    $form->save->label = t('Try again', 'Form');
                }
                $form->show();
                break;
            case 'finish':
                $tpl = new Template('Install/templates/finish');
                $tpl->show();
                break;
            case 'done':
                $config = new Config(SITE . DS . 'site' . EXT);
                $config->installed = TRUE;
                $config->store(TRUE);
                flash_success(t('Your site has been successfully configured!', 'Install'));
                redirect();
                break;
            default:
            case 'welcome':
                $tpl = new Template('Install/templates/welcome');
                $tpl->show();
        }
    }

}