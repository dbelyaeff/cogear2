<?php

return array(
    '#name' => 'install',
    'sitename' => array(
        '#type' => 'text',
        '#label' => t('Название сайта'),
        '#validators' => array('Required'),
        '#value' => config('site.name'),
    ),
    'database' => array(
        '#type' => 'fieldset',
        '#class' => 'collapsed',
        '#label' => t('Настройка соединения с базой данных'),
        'host' => array(
            '#type' => 'text',
            '#validate' => array('Required'),
            '#label' => t('Хост'),
            '#value' => 'localhost',
            '#placeholder' => 'localhost',
        ),
        'base' => array(
            '#type' => 'text',
            '#validate' => array('Required'),
            '#label' => t('Название базы'),
            '#value' => 'cogear',
        ),
        'user' => array(
            '#type' => 'text',
            '#validate' => array('Required'),
            '#label' => t('Имя пользователя'),
            '#value' => 'root',
        ),
        'pass' => array(
            '#type' => 'password',
            '#label' => t('Пароль'),
        ),
        'port' => array(
            '#type' => 'text',
            '#label' => t('Порт'),
            '#validate' => array('Num')
        ),
        'prefix' => array(
            '#type' => 'text',
            '#label' => t('Префикс таблиц'),
        ),
        'create_db' => array(
            '#type' => 'checkbox',
            '#label' => t('Попытаться создать базу данных'),
        ),
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'save' => array(
            '#type' => 'submit',
            '#label' => t('Далее'),
            '#class' => 'btn btn-primary',
        ),
    )
);