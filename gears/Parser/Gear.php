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
        'parse' => 'hookParse',
    );
    protected $access = array(
        'off' => array(1),
        'admin' => array(1),
    );
    protected $routes = array(
        'admin/parser' => 'admin_action',
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
     * Хук парсера
     *
     * @param type $item
     */
    public function hookParse($item){
        // Автоматический парсинг перевода строки
        if(config('Parser.nl2br') && $item->body){
            $item->body = preg_replace('#\>([\n\r\t\s]+)\<#imsU','><',$item->body);
            $item->body = preg_replace('#\<br/?\>#imsU',"",$item->body);
            $item->body = nl2br($item->body);
        }
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
    /**
     * Настройки
     */
    public function admin_action(){
        $form = new Form(array(
            '#name' => 'admin.parser',
            'title' => array(
                'label' => t('Настройки'),
            ),
            'nl2br' => array(
                'type' => 'checkbox',
                'label' => t('Автоматическая обработка строк'),
                'value' => config('Parser.nl2br'),
            ),
            'save' => array()
        ));
        if($result = $form->result()){
            $this->set('Parser.nl2br',$result->nl2br);
            flash_success(t('Настройки сохранены!'));
            reload();
        }
        $form->show();
    }
}