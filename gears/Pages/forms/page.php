<?php

return array(
    'name' => 'page',
    'elements' => array(
        'name' => array(),
        'pid' => array(
            'label' => t('Родительская страница'),
            'type' => 'select',
            'values' => array(),
        ),
        'link' => array(
            'validators' => array('Required', 'Pages_Validate_Link'),
            'filters' => array(),
            // 'description' => t(''),
            'value' => isset($_GET['uri']) ? $_GET['uri'] : '',
        ),
        'body' => array(),
        'show_title' => array(
            'type' => 'checkbox',
            'label' => t('Показывать заголовок'),
        ),
        'show_breadcrumb' => array(
            'type' => 'checkbox',
            'label' => t('Показывать навигационную панель'),
        ),
        'actions' => array(
            'elements' => array(
                'save' => array(),
                'delete' => array(),
            )
        ),
    )
);