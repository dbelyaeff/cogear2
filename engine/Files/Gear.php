<?php

/**
 * Files gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Files_Gear extends Gear {

    protected $name = 'Files manager';
    protected $description = 'Files manager';
    protected $package = '';
    protected $order = -10;
    protected $hooks = array(
        'user.profile.fields' => 'hookUserProfile',
        'markitup.toolbar' => 'hookMarkItUp',
    );

    /**
     * Extend MarkItUp toolbar
     *
     * @param type $toolbar
     */
    public function hookMarkItUp($toolbar) {
        if (access('files.manager')) {
            $toolbar->markupSet->append(array(
                'name' => 'Files manager',
                'key' => 'M',
                'className' => 'markItUpFileManager',
                'call' => 'showElFinder',
            ));
        }
    }

    /**
     * hook Menu
     *
     * @param type $name
     * @param type $menu
     */
    public function menu($name, $menu) {
        if (access('files.manager')) {
            if ($name == 'user.profile.tabs') {
                $menu->register(array(
                    'label' => t('Files', 'elFinder'),
                    'link' => l('/files'),
                ));
            } else if ($name == 'navbar') {
                $menu->register(array(
                    'label' => icon('folder-open icon-white'),
                    'link' => l('/files/'),
                    'place' => 'left',
                    'order' => 10,
                ));
            }
        }
    }

    /**
     * Ignore assets autoloading
     */
    public function loadAssets() {
        //parent::loadAssets();
    }

    /**
     * Init
     */
    public function init() {
        if (access('files.manager')) {
            $elFinder = new Files_Manager();
            $elFinder->load();
            js($this->folder.'/js/markitup.js');
        }
        parent::init();
    }
    /**
     * Index action
     */
    public function index_action() {
        if (Ajax::is()) {
            Theme::$layout = 'splash';
        } else {
            $this->user->navbar()->show();
        }
        $files_manager = new Files_Manager();
        $files_manager->show();
    }

    /**
     * Connector action
     */
    public function connector_action() {
        if (access('files.manager')) {
            $options = array(
                'debug' => true,
                'roots' => array(
                    array(
                        'alias' => $this->user->getName(),
                        'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                        'path' => $this->user->dir(), // path to files (REQUIRED)
                        'URL' => File::pathToUri($this->user->dir()), // URL to files (REQUIRED),
                        'mimefile' => $this->dir . DS . 'mime.types',
                        'uploadDeny' => array('All'),
                        'uploadAllow' => array('image/jpg', 'image/jpeg', 'image/png'),
                        'uploadOrder' => array('deny', 'allow'),
                        'uploadMaxSize' => config('elFinder.uploadMaxSize', '100K'),
                        'acceptedName' => array($this, 'checkFilename'),
                        'accessControl' => array($this, 'access'),
                ))
            );
            $connector = new Files_Connector(new Files_Handler($options));
            $connector->run();
        }
    }

    /**
     *
     * @param type $attr
     * @param type $path
     * @param type $data
     * @param type $volume
     * @return type
     */
    public function access($attr, $path, $data, $volume) {
        return strpos(basename($path), '.') === 0 ? !($attr == 'read' || $attr == 'write') : null;
    }

    /**
     * Check Filename
     *
     * @param type $name
     * @return type
     */
    public function checkFilename($name) {
        return TRUE;
    }

}