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
            'validators' => array('Required','Pages_Validate_Link'),
            'filters' => array(),
           // 'description' => t(''),
            'value' => isset($_GET['uri']) ? $_GET['uri'] : '',
        ),
        'save' => array(),
    )
);