<?php
return array(
    '#name' => 'menu',
    'name' => array(

    ),
    'machine_name' => array(
        'type' => 'text',
        'label' => t('Имя в системе'),
        'description' => t('Только маленькими английскими буквами. Будет использовано для вывода класса в шаблоне.'),
        'validate' => array('Required',array('Regexp','([a-z]+)',t('Допустимы только маленькие английские буквы.'))),
    ),
    'template' => array(
        'type' => 'select',
        'values' => array(),
        'class' => 'input-xxlarge',
        'label' => t('Шаблон меню'),
        'description' => t('Выберите шаблон, который будет формировать меню.'),
        'validate' => array('Required'),
    ),
    'multiple' => array(
        'type' => 'checkbox',
        'label' => t('Возможность делать активными сразу несколько пунктов меню'),
    ),
    'title' => array(
        'type' => 'checkbox',
        'label' => t('Формировать заголовок страницы'),
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'save' => array(),
        'delete' => array(),
    )
);