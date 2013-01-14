<?php

return array(
    '#name' => 'theme.widget',
    'name' => array(
        'description' => t('Название виджета будет фигурировать только в панели управления.'),
    ),
    'callback' => array(
        'type' => 'select',
        'label' => t('Тип виджета'),
        'values' => array(),
        'validate' => array('Required'),
        'class' => 'input-xxlarge',
    ),
    'route' => array(
        'type' => 'text',
        'label' => t('Путь для отображения'),
        'description' => t('Виджет будет отображаться только по этому адресу. Можно использовать регулярные выражения.'),
        'value' => '.*',
        'placeholder' => t('Путь страницы или регулярное выражение…'),
        'validate' => array('Required'),
    ),
    'region' => array(
        'type' => 'select',
        'label' => t('Регион вывода'),
        'values' => array(),
        'validate' => array('Required'),
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'save' => array(),
        'delete' => array(),
    )
);