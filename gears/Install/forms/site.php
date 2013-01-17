<?php

return array(
    '#name' => 'install',
    'sitename' => array(
        '#type' => 'text',
        '#label' => t('Название сайта'),
        '#validators' => array('Required'),
        '#value' => config('site.name'),
    ),
    'sitehost' => array(
        '#type' => 'text',
        '#validate' => array('Required'),
        '#label' => t('Адрес сайта'),
        '#value' => server('HTTP_HOST'),
    ),
    'database' => array(
        '#type' => 'fieldset',
        '#class' => 'collapsed',
        '#label' => t('Настройка соединения с базой данных'),
        'host' => array(
            '#type' => 'text',
            '#validate' => array('Required'),
            '#label' => t('Хост'),
            '#value' => config('database.host'),
            '#placeholder' => 'localhost',
        ),
        'base' => array(
            '#type' => 'text',
            '#validate' => array('Required'),
            '#label' => t('Название базы'),
            '#value' => config('database.base'),
        ),
        'user' => array(
            '#type' => 'text',
            '#validate' => array('Required'),
            '#label' => t('Имя пользователя'),
            '#value' => config('database.user'),
        ),
        'pass' => array(
            '#type' => 'password',
            '#label' => t('Пароль'),
            '#value' => config('database.pass'),
        ),
        'port' => array(
            '#type' => 'text',
            '#label' => t('Порт'),
            '#validate' => array('Num'),
            '#value' => config('database.port'),
        ),
        'prefix' => array(
            '#type' => 'text',
            '#label' => t('Префикс таблиц'),
            '#value' => config('database.prefix'),
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