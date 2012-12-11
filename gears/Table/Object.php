<?php

/**
 * Объект Таблицы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Table_Object extends Object {

    /**
     * Options
     *
     * @var array
     */
    public $options = array(
        'name' => 'name',
        'class' => 'table',
        'fields' => array(),
        'template' => 'Table/templates/table'
    );

    /**
     * Get Items
     *
     * @return array
     */
    public function getItems() {
        $items = new Core_ArrayObject();
        if ($this->object && $this->object()->count()) {
            $i = 0;
            $unset = array();
            foreach ($this->object as $item) {
                foreach ($this->fields as $key => $field) {
                    if (FALSE === $field->access) {
                        $unset[] = $key;
                        continue;
                    }
                    $name = $field->source ? $field->source : $key;
                    $items[$i][$key] = new Core_ArrayObject();
                    $field->class && $items[$i][$key]->class = $field->class;
                    if ($field->callback && $field->callback instanceof Callback) {
                        $field->callback->setArgs(array($item, $key));
                        $items[$i][$key]->value = $field->callback->run();
                    } else {
                        $items[$i][$key]->value = $item->$name;
                    }
                    if ($field->template) {
                        $items[$i][$key]->value = sprintf($field->template, $items[$i][$key]->value);
                    }
                }
                $i++;
            }
            if ($unset) {
                foreach ($unset as $key) {
                    $this->fields->offsetUnset($key);
                }
            }
        }
        return $items;
    }

    /**
     * Render
     */
    public function render() {
        event('table.render', $this);
        event('table.render.' . $this->name, $this);
        $tpl = new Template($this->template);
        $tpl->table = $this->name;
        $tpl->class = $this->class;
        $tpl->fields = $this->fields;
        $tpl->items = $this->getItems();
        return $tpl->render();
    }

}