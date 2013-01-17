<?php

return array(
    '#name' => 'menu.item',
    'label' => array(
        'type' => 'text',
        'label' => t('Текст ссылки'),
        'validate' => array('Required'),
    ),
    'pid' => array(
        'label' => t('Родительский пункт'),
        'type' => 'select',
        'values' => array(),
    ),
    'link' => array(
        'type' => 'text',
        'label' => t('Ссылка'),
        'validate' => array('Required'),
        'value' => '/',
        'description' => '',
        // Важно! Снимаем фильтр по умолчанию, который в  defaults.php прописан
        'filter' => array(),
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'save' => array(),
        'delete' => array(),
    )
);