<?php

/**
 *  gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Test_Gear extends Gear {

    protected $name = '';
    protected $description = '';
    protected $package = '';
    protected $order = 0;
    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index($action = '', $subaction = NULL) {
        $this->db->where_in('id', array())->order('id', 'ASC');
        $blog = new Blog();
        if ($result = $blog->findAll()) {
            foreach ($result as $blog) {
//                $result[$blog->id] = $blog->name;
            }
        }
    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function action_index($subaction = NULL) {

    }

}