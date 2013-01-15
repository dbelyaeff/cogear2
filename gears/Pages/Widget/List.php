<?php

/**
 * Виджет списка страниц
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Pages_Widget_List extends Theme_Widget_Abstract {
    /**
     * Настройки по умолчанию
     *
     * @var array
     */
    protected $options = array(
        'root' => '1',
    );

    /**
     * Вывод
     *
     * @return string
     */
    public function render() {
        return cogear()->pages->renderList($this->options->root);
    }

    /**
     * Настройки
     */
    public function settings() {
        $form = new Form(array(
                    '#name' => 'widget.pages.list',
                    'root' => array(
                        'type' => 'select',
                        'validate' => array('Required'),
                        'value' => $this->options->root,
                        'values' => page()->getSelectValues(),
                        'label' => t('Выберите корневую страницу'),
                    ),
                    'actions' => array(
                        '#class' => 'form-actions',
                        'save' => array(),
                    ),
                ));
        if ($result = $form->result()) {
            $this->options->root = $result->root;
            if ($this->save()) {
                return TRUE;
            }
        }
        $form->show();
    }

}