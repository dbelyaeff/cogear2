<?php
return array(
    'name' => 'gears-add',
    'title' => t('Загрузить шестерёнку'),
    'elements' => array(
        'file' => array(
            'label' => t('С диска'),
            'type' => 'file',
            'allowed_types' => array('zip','tar.gz'),
            'maxsize' => 3072,
            'path' => UPLOADS.DS.'gears',
            'overwrite' => TRUE,
        ),
        'or' => array(
            'type' => 'div',
            'value' => '<h2>OR</h2>',
        ),
        'url' => array(
            'type' => 'file_url',
            'label' => t('С Интернета'),
            'allowed_types' => array('zip','tar.gz'),
            'maxsize' => 3072,
            'path' => UPLOADS.DS.'gears',
            'overwrite' => TRUE,
            'validators' => array('Url'),
        ),
        'actions' => array(
            'elements' => array(
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Загрузить'),
                )
            )
        )
    )
);