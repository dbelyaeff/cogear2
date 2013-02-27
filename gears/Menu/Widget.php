<?php

/**
 * Виджет HTML-кода
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Menu_Widget extends Theme_Widget_Abstract {

    /**
     * Настройки по умолчанию
     *
     * @var array
     */
    protected $options = array(
        'id' => '',
    );

    /**
     * Вывод
     *
     * @return string
     */
    public function render() {
        if ($menu = menu($this->options->id)) {
            return $menu->render();
        } else {
            return '';
        }
    }

    /**
     * Настройки
     */
    public function settings() {
        $handler = menu();
        $menus = array();
        if($result = $handler->findAll()){
            foreach($result as $menu){
                $menus[$menu->id] = $menu->name;
            }
        }
        $form = new Form(array(
                    '#name' => 'widget.menu',
                    'id' => array(
                        'type' => 'select',
                        'validate' => array('Required'),
                        'label' => t('Выберите меню'),
                         'value' => $this->options->id,
                         'values' => $menus,
                    ),
                    'actions' => array(
                        '#class' => 'form-actions',
                        'save' => array(),
                    ),
                ));
        if ($result = $form->result()) {
            $this->options->id = $result->id;
            if ($this->save()) {
                return TRUE;
            }
        }
        $form->show();
    }

}