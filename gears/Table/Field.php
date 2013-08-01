<?php

/**
 * Поле таблицы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Table_Field extends Object {

    protected $name = '';
    protected $options = array(
        'name' => '',
        'align' => 'left',
        'width' => '',
        'template' => '',
        'callback' => '',
    );

    /**
     * Конструктор
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = NULL) {
        $this->name = $name;
        parent::__construct($options);
    }

    /**
     * Вывод таблицы
     *
     * @return string
     */
    public function render() {
        if ($this->options->template) {
            $tpl = new Template($this->options->template);
            $tpl->data = $this->object();
            return $tpl->render();
        } elseif($this->options->callback instanceof Callback){
          return $this->options->callback->run(array($this->name,$this->object()));
        } else {
            return $this->object() instanceof Core_ArrayObject && $this->object()->{$this->name} !== NULL ? $this->object()->{$this->name} : '';
        }
    }

}