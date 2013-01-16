<?php

/**
 * Дерево элементов с Drag'n'Drop
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Db_Tree_DDList extends Options {

    protected $options = array(
        'class' => '',
        'items' => '',
        'render' => 'content',
        'template' => 'Db/templates/tree/dd',
        'saveUri' => '/db/saveDDtree',
    );

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->render && hook($this->render, array($this, 'show'));
    }

    /**
     * Вывод
     */
    public function render() {

        $items = array();
        if ($this->options->class) {
            if (!class_exists($this->options->class)) {
                return error(t('Класс <b>%s</b> не существует.', $this->options->class));
            }
            $object = new $this->options->class();
            $items = $object->findAll();
        } elseif ($this->options->items) {
            $items = $this->options->items;
        }
        if ($items) {
            $template = new Template($this->template);
            $template->options = $this->options;
            $template->items = $items;
            js(cogear()->db->folder . '/js/inline/jquery.nestable.js');
            js(cogear()->db->folder . '/js/inline/nest.js');
            return $template->render();
        }
    }

}