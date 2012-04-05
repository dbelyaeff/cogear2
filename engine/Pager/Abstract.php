<?php
/**
 * Abstract pager
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Pager
 * @version		$Id$
 */
abstract class Pager_Abstract extends Object{
    protected $count = 0;
    protected $base_uri;
    protected $per_page = 5;
    protected $pages_num;
    protected $is_initiated;
    protected $ajaxed;
    protected $target;

    /**
     * Constructor
     * 
     * @param array $options
     */
    public function __construct($options = array()) {
       parent::__construct($options,Options::SELF);
       $this->init();
    }
    abstract public function init();
}