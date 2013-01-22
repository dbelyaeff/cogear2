<?php

/**
 * Шестеренка Parser
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Parser_Gear extends Gear {

    protected $hooks = array(
        'form.load' => 'hookFormLoad',
    );
    protected $access = array(
        'off' => array(1),
    );
    /**
     * Конструтктор
     *
     * @param type $config
     */
    public function __construct($config) {
        parent::__construct($config);
    }

    /**
     * Хук инициализации формы
     *
     * @param type $Form
     */
    public function hookFormLoad($Form) {
        if (access('Parser.off')) {
            if ($this->input->post('parser_off')) {
                Cookie::set('parser_off', TRUE);
            }
            if ($Form->body) {
                $Form->add('parser_off', array(
                    'type' => 'checkbox',
                    'label' => t('Отключить парсер'),
                    'value' => Cookie::get('parser_off') ? TRUE : FALSE,
                    'order' => $Form->body->options->order . '.1'
                ));
            }
        }
    }
}