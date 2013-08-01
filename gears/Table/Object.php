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

    protected $options = array(
        'name' => '',
        'class' => 'table',
        'fields' => array(),
        'header' => TRUE,
        'footer' => TRUE,
        'render' => 'content',
    );

    /**
     * Конструктор
     *
     * @param array $options
     * @param mixed  $place
     */
    public function __construct($options = NULL, $place = NULL) {
        parent::__construct(Options::decode($options, 'fields'), $place);
    }

    /**
     * Render
     */
    public function render() {
        event('table.render', $this);
        if (!$this->object()) {
            return event('empty');
        }
        $tpl = new Template('Table/templates/table');
        $tpl->options = $this->options;
        $tpl->fields = new Core_ArrayObject();
        $tpl->thead = '';
        $tpl->tbody = new Core_ArrayObject();
        $tpl->tfoot = '';
        foreach ($this->fields as $name => $config) {
            $tpl->fields->$name = new Table_Field($name, $config);
        }
        if ($this->options->header) {
            $tpl->thead = '<thead><tr>';
            foreach ($tpl->fields as $name => $field) {
                $tpl->thead .= '<th';
                if ($field->align) {
                    $tpl->thead .= ' align="' . $field->align . '" ';
                }
                if ($field->width) {
                    $tpl->thead .= ' width="' . $field->width . '" ';
                }
                $tpl->thead .= '>' . $field->label . '</th>';
            }
            $tpl->thead .= '</tr></thead>' . "\n";
        }
        foreach ($this->object() as $data) {
            $row = '<tr>';
            foreach ($tpl->fields as $name => $field) {
                $row .= '<td';
                if ($field->align) {
                    $row .= ' align="' . $field->align . '" ';
                }
                if ($field->width) {
                    $row .= ' width="' . $field->width . '" ';
                }
                $row .= '>';
                $field->object($data);
                $row .= $field->render();
                $row .= '</td>';
            }
            $row .= '</tr>';
            $tpl->tbody->append($row);
        }
        if ($this->options->footer) {
            $tpl->tfoot = '<tfoot><tr>';
            foreach ($tpl->fields as $name => $field) {
                $tpl->tfoot .= '<th>' . $field->label . '</th>';
            }
            $tpl->tfoot .= '</tr></tfoot>' . "\n";
        }
        return $tpl->render();
    }

}