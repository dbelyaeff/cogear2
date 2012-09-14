<?php
d('Gears');

return array(
    'name' => 'gears-add',
    'title' => t('Add gears'),
    'elements' => array(
        'file' => array(
            'label' => t('From disk'),
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
            'label' => t('From url'),
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
                    'label' => t('Upload'),
                )
            )
        )
    )
);