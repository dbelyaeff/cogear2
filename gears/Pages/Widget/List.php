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
        'template' => 'Pages/templates/list',
        'current' => FALSE,
    );

    /**
     * Вывод
     *
     * @return string
     */
    public function render() {
        if (!$render = cache('widgets/' . $this->object()->id)) {
            if($this->options->current && NULL === cogear()->pages->current){
                return;
            }
            $tpl = new Template($this->template);
            if ($tpl->pages = cogear()->pages->getList($this->options->current ? cogear()->pages->current->id : $this->options->root, FALSE)) {
                $render = $tpl->render();
            }
            else {
                $render = '';
            }
            cache('widgets/' . $this->object()->id, $render, array('pages'));
        }
        return $render;
    }

    /**
     * Переопределение сохранения с чисткой кеша
     *
     * @return boolean
     */
    public function save() {
        cogear()->cache->remove('widgets/' . $this->object()->id);
        return parent::save();
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
                    'current' => array(
                        'type' => 'checkbox',
                        'label' => t('Использовать текущую страницу как корневую'),
                        'value' => $this->options->current,
                    ),
                    'template' => array(
                        'type' => 'text',
                        'value' => $this->options->template,
                        'label' => t('Шаблон для вывода'),
                        'description' => t('Будьте внимательны! Указывайте только существующий шаблон во избежание ошибок.'),
                    ),
                    'actions' => array(
                        '#class' => 'form-actions',
                        'save' => array(),
                    ),
                ));
        inline_js('$(document).ready(function(){
            $("input[type=checkbox]").on("change",function(){
                if($(this).attr("checked")){
                    $("#form-widget-pages-list-root").slideUp("fast");
                }
                else {
                    $("#form-widget-pages-list-root").slideDown("fast");
                }
            }).trigger("change");

        })');
        if ($result = $form->result()) {
            $this->options->root = $result->root;
            $this->options->template = $result->template;
            $this->options->current = (bool) $result->current;
            if ($this->save()) {
                return TRUE;
            }
        }
        $form->show();
    }

}