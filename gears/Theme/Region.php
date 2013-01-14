<?php

/**
 * Регион темы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Theme_Region extends Options {

    /**
     * Конструктор
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = array()) {
        $options['name'] = $name;
        parent::__construct($options);
    }

    /**
     * Подготовка вывода
     *
     * @return string
     */
    public function output() {
        $output = new Core_ArrayObject();
        foreach ($this as $item) {
            if ($item instanceof Callback) {
                $output->append($item->run());
            } else {
                $output->append($item);
            }
        }
        if ('widgets' === cogear()->input->get('mode') && access('Theme.widgets')) {
            echo template('Theme/templates/widgets/wrapper',array('output'=>$output,'name'=>$this->name))->render();
        }
        elseif($output) {
            echo $output;
        }
    }

    /**
     * Отбражение региона
     *
     * @return string
     */
    public function render() {
        // Делаем всё через хуки, чтобы другие тоже могли выводить
        hook($this->options->name, array($this, 'output'));
        ob_start();
        event($this->options->name);
        return ob_get_clean();
    }

}