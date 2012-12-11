<?php

/**
 * Pages pager
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Pager

 */
class Pager_Object extends Pager_Abstract {

    public $options = array(
        'current' => 0,
        'count' => 0,
        'per_page' => 5,
        'order' => self::FORWARD,
        'base' => '',
        'show_empty' => FALSE,
        'autolimit' => TRUE,
        'prefix' => 'page',
        'method' => 1,
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
    const PATH = 0;
    const GET = 1;

    /**
     * Конструктор
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
        // Auto parse page from uri
        if ($this->current == 0) {
            switch ($this->method) {
                case self::PATH:
                    if ($uri = cogear()->router->getUri()) {
                        if (preg_match('#' . preg_quote($this->prefix) . '([\d+])/?$#', $uri, $matches)) {
                            $this->options->current = $matches[1];
                        }
                    }
                    break;
                case self::GET:
                    $this->options->current = cogear()->input->get($this->prefix);
                    break;
            }
        }
        if ($this->options->per_page == 0) {
            $this->options->per_page = config('per_page', 5);
        }
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
        $this->options->autolimit && cogear()->db->limit($this->per_page, $this->start);
        if ($this->first == $this->current) {
            $this->first = NULL;
            $this->prev = NULL;
        }
        if ($this->first == $this->prev) {
            $this->first = NULL;
        }
        if ($this->last == $this->current) {
            $this->next = NULL;
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
            $tpl = new Template('Pager/templates/pages');
            if (isset($_GET[$this->prefix])) {
                unset($_GET[$this->prefix]);
            }
            $tpl->assign(array(
                'count' => $this->count,
                'current' => $this->current,
                'pages_num' => $this->pages_num,
                'first' => $this->first,
                'last' => $this->last,
                'prev' => $this->prev,
                'next' => $this->next,
                'order' => $this->order,
                'base' => $this->base,
                'prefix' => $this->method == self::GET ? '?' . ($_GET ? http_build_query($_GET) . '&' : '') . $this->prefix . '=' : $this->prefix,
                'options' => $this->options,
            ));
            return $tpl->render();
        }
        return;
    }

}