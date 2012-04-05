<?php

/**
 * Install gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Install
 * @version		$Id$
 */
class Install_Gear extends Gear {

    protected $name = 'Install';
    protected $description = 'Help to install system.';
    protected $package = 'Core';
    protected $order = 0;

    /**
     * Init
     */
    public function init() {
        parent::init();
        if (!cogear()->default) {
            $this->router->addRoute(':index', array($this, 'index'), TRUE);
            new Install_Menu();
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
            $root = Url::gear('install');
            d('Install');
            $menu->{$root . 'welcome'} = t('1. Welcome.');
            $menu->{$root . 'requirements'} = t('2. Requirements.');
            $menu->{$root . 'site'} = t('3. Site info.');
            $menu->{$root . 'theme'} = t('4. Theme.');
            $menu->{$root . 'finish'} = t('10. Finish.');
            d();
            !$this->router->getSegments() && $menu->setActive($root . 'welcome');
        }
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($action = '') {
        switch ($action) {
            case 'requirements':
                $tpl = new Template('install.requirements');
                $tpl->show();
                break;
            case 'site':
                append('content', t('Define basic settings for your site.', 'Install'));
                $form = new Form('install.site');
                $form->init();
                $form->sitename->setValue(config('site.name'));
                if ($result = $form->result()) {
                    $config = new Config(SITE . DS . 'settings' . EXT);
                    $config->site->name = $result->sitename;
                    $config->key = md5(md5(time()) + time() + $config->site->name);
                    $config->database->dsn = $result->database;
                    $config->store();
                    redirect('/install/theme');
                } else {
                    $form->save->label = t('Try again', 'Form');
                }
                $form->show();
                break;
            case 'theme':
                append('content', t('It\'s now time to choose site theme.', 'Install'));
                $themes = $this->theme->searchThemes();
                $form = new Form('install.theme');
                $form->init();
                $values = array();
                foreach ($themes as $name => $theme) {
                    $values[$name] = $name;
                }
                $form->theme->setValues($values);
                if ($result = $form->result()) {
                    cogear()->set('theme.current', $result->theme);
                    redirect('/install/finish/');
                }
                $form->show();
                break;
            case 'finish':
                $tpl = new Template('install.finish');
                $tpl->show();
                break;
            case 'done':
                cogear()->set('installed', TRUE);
                cogear()->activate('default');
                flash_success(t('Your site has been successfully configured!', 'Install'));
                redirect();
                break;
            default:
            case 'welcome':
                cogear()->deactivate('db');
                cogear()->deactivate('default');
                $tpl = new Template('install.welcome');
                $tpl->show();
        }
    }

}