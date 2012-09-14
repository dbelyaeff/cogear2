<?php

/**
 * Zip gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Zip_Gear extends Gear {

    protected $name = 'Zip';
    protected $description = 'Extract and pack zip archives';
    protected $package = '';
    protected $order = 0;
    protected $is_core = TRUE;
    protected $hooks = array(
    );
    protected $routes = array(
    );
    protected $access = array(
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            case 'create':
                return TRUE;
                break;
        }
        return FALSE;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        if(!class_exists('ZipArchive')){
            error(t('You need to install PHP ZIP module to work Zip gear correctly.','Zip'));
        }
        else {
            $this->object(new ZipArchive());
        }
    }

    /**
     * Hook menu
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {

        }
    }


}