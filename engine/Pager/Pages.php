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
class Pager_Pages extends Pager_Abstract {
    protected $current;
    protected $prev;
    protected $next;
    protected $first;
    protected $last;
    protected $method = 0;
    protected $order = 0;
    const URI = 0;
    const GET = 1;
    const FORWARD = 0;
    const REVERSE = 1;
    const ARG = 'page';

    /**
     * Init pager
     */
    public function init() {
        $this->pages_num = round($this->count / $this->per_page);
        if ($this->order == self::FORWARD) {
            $this->first = 1;
            $this->last = $this->pages_num;
        } else {
            $this->first = $this->pages_num;
            $this->last = 1;
        }
        $this->current OR $this->current = $this->first;
        if ($this->order == self::FORWARD) {
            $start = $this->per_page*($this->current-1);
            $this->prev = $this->current - 1;
            $this->next = $this->current + 1;
        } else {
            $start = ($this->first - $this->current) * $this->per_page;
            $this->prev = $this->current + 1;
            $this->next = $this->current - 1;
        }
        cogear()->db->limit($start, $this->per_page);
    }

    /**
     * Render
     */
    public function render() {
        $this->is_initiated OR $this->init();
        $tpl = new Template('Pager.pages');
        $tpl->assign(array(
            'count' => $this->count,
            'current' => $this->current,
            'first' => $this->first,
            'last' => $this->last,
            'prev' => $this->prev,
            'next' => $this->next,
            'method' => $this->method,
            'order' => $this->order,
            'base_uri' => $this->base_uri,
            'ajaxed' => $this->ajaxed,
            'target' => $this->target,
        ));
        return $tpl->render();
    }

}