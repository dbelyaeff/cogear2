<?php

/**
 * Db list
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Db_List extends Object {

    protected $class = 'Pages_Object';
    protected $template = 'Db/templates/list';
    protected $options = array(
        'dragndrop' => FALSE,
        'render' => 'content',
    );

    /**
     *
     * @param type $options
     */
    public function __construct($options = NULL) {
        parent::__construct($options);
        if ($this->options->render) {
            // If $render is set to TRUE make it 'content'
            TRUE === $this->options->render && $this->options->render = 'content';
            append($this->options->render, new Callback(array($this, 'hookRender')));
        }
    }

    /**
     * Hook render
     */
    public function hookRender() {
        echo $this->render();
    }

    /**
     * Render
     */
    public function render() {
        $items = new $this->class();
        if($this->options->where){
            $items->where($this->options->where);
        }
        if($this->options->order){
            foreach($this->options->order as $field=>$direction){
                $items->order($field,$direction);
            }
        }
        if ($result = $items->findAll()) {
            $tpl = new Template($this->template);
            $tpl->options = $this->options;
            $tpl->items = $result;
            return $tpl->render();
        } else {
            event('empty');
        }
    }

}