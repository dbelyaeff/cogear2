<?php

return array(
    '#name' => 'code',
    'name' => array(
        '#type' => 'text',
        '#label' => t('Название сниппета'),
    ),
    'code_editor' => array(
        '#type' => 'div',
        '#label' => '',
    ),
    'type' => array(
        '#type' => 'select',
        '#label' => t('Режим работы редактора: '),
        '#values' => array(
            'php' => 'PHP',
            'javascript' => 'JavaScript',
            'css' => 'CSS',
            'html' => 'HTML',
            'sql' => 'SQL',
        )
    ),
    'code' => array(
        '#type' => 'textarea',
        '#class' => 'hidden',
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'submit' => array(
            '#type' => 'submit',
            '#class' => 'btn btn-primary',
            '#label' => t('Сохранить'),
        ),
        'delete' => array(
        ),

    )
);