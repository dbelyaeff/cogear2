<?php

return array(
    'name' => array(
        'label' => t('Название'),
        'type' => 'text',
        'validators' => array('Required', array('Length', 3)),
        'class' => 'ajaxed',
    ),
    'link' => array(
        'type' => 'text',
        'label' => t('Ссылка'),
        'validators' => array(array('Length', 3)),
        'filters' => array('Uri'),
        'placeholder' => array('Введите ссылку…'),
        'description' => t('Параметр будет использован для формирования ссылки. Если оставить поле пустым, значение будет сгенерировано автоматически.'
    )),
    'body' => array(
        'type' => 'editor',
        'label' => t('Текст'),
        'filters' => array('Jevix_Filter'),
        'validators' => array('Required', array('Length', 1)),
    ),
    'description' => array(
        'type' => 'editor',
        'label' => t('Описание'),
        'filters' => array('Jevix_Filter'),
        'validators' => array('Required', array('Length', 1)),
    ),
    'actions' => array(
        'type' => 'group',
        'class' => 'form-actions',
    ),
    'delete' => array(
        'type' => 'delete',
        'class' => 'fl_r',
        'label' => t('Удалить')
    ),
    'preview' => array(
        'type' => 'submit',
        'class' => 'btn btn-inverse',
        'label' => t('Предпросмотр'),
    ),
    'draft' => array(
        'type' => 'submit',
        'class' => 'btn',
        'label' => t('В черновики'),
    ),
    'publish' => array(
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'label' => t('Опубликовать'),
    ),
    'buttons' => array(
        'type' => 'group',
        'class' => 'btn-group',
    ),
    'title' => array(
        'type' => 'title',
    ),
    'submit' => array(
        'type' => 'submit',
        'label' => t('Отправить'),
        'class' => 'btn btn-primary',
    ),
    'save' => array(
        'type' => 'submit',
        'label' => t('Сохранить'),
        'class' => 'btn btn-primary',
    ),
    'update' => array(
        'type' => 'submit',
        'label' => t('Обновить'),
        'class' => 'btn btn-primary',
    ),
);