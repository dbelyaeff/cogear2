<?php

/**
 * Виджет HTML-кода
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Theme_Widget_HTML extends Theme_Widget_Abstract {
    /**
     * Настройки по умолчанию
     *
     * @var array
     */
    protected $options = array(
        'title' => '',
        'content' => '',
    );

    /**
     * Вывод
     *
     * @return string
     */
    public function render() {
        return template('Theme/Widget/templates/HTML', $this->options)->render();
    }

    /**
     * Настройки
     */
    public function settings() {
        $form = new Form(array(
                    '#name' => 'widget.html',
                    'title' => array(
                        'type' => 'text',
                        'label' => t('Заголовок'),
                        'value' => $this->options->title,
                    ),
                    'content' => array(
                        'type' => 'editor',
                        'filter' => array('Parser_Filter'),
                        'validate' => array('Required'),
                        'value' => $this->options->content
                    ),
                    'actions' => array(
                        '#class' => 'form-actions',
                        'save' => array(),
                    ),
                ));
        if ($result = $form->result()) {
            $this->options->title = $result->title;
            $this->options->content = $result->content;
            if ($this->save()) {
                return TRUE;
            }
        }
        $form->show();
    }

}