<?php

/**
 * Pages pager
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Pager
 * @version		$Id$
 */
class Pager_Object extends Pager_Abstract {

    public $options = array(
        'current' => 0,
        'count' => 0,
        'per_page' => 5,
        'order' => self::FORWARD,
        'base_uri' => '',
        'show_empty' => FALSE,
        'autolimit' => TRUE,
    );
    protected $prev;
    protected $next;
    protected $first;
    protected $last;
    protected $pages_num;
    protected $is_init;
    public $start;
    const FORWARD = 0;
    const REVERSE = 1;

    /**
     * Constructor
     *  
     * @param type $options 
     */
    public function __construct($options = NULL) {
        parent::__construct($options);
        $this->init();
    }

    /**
     * Init pager
     */
    protected function init() {
        $this->pages_num = ceil($this->count / $this->per_page);
        if ($this->order == self::FORWARD) {
            $this->first = 1;
            $this->last = $this->pages_num;
        } else {
            $this->first = $this->pages_num;
            $this->last = 1;
        }
        if (!$this->current) {
            $this->options->current = $this->first;
        }
        if ($this->order == self::FORWARD) {
            $this->start = $this->per_page * ($this->current - 1);
            $this->prev = $this->current - 1;
            $this->next = $this->current + 1;
        } else {
            $this->start = ($this->first - $this->current) * $this->per_page;
            $this->prev = $this->current + 1;
            $this->next = $this->current - 1;
        }
        $this->options->autolimit && cogear()->db->limit($this->start, $this->per_page);
        if ($this->first == $this->current) {
            $this->first = NULL;
            $this->prev = NULL;
        }
        if ($this->first == $this->prev) {
            $this->first = NULL;
        }
        if ($this->last == $this->current) {
            $this->next= NULL;
            $this->last = NULL;
        }
        if ($this->last == $this->next) {
            $this->last = NULL;
        }
        $this->is_init = TRUE;
    }

    /**
     * Render
     */
    public function render() {
        $this->is_init OR $this->init();
        if ($this->show_empty OR $this->per_page < $this->count) {
            $tpl = new Template('Pager.pages');
            $tpl->assign(array(
                'count' => $this->count,
                'current' => $this->current,
                'pages_num' => $this->pages_num,
                'first' => $this->first,
                'last' => $this->last,
                'prev' => $this->prev,
                'next' => $this->next,
                'order' => $this->order,
                'base_uri' => $this->base_uri,
            ));
            return $tpl->render();
        }
        return;
    }

}