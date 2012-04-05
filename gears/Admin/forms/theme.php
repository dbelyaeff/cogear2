<?php

return array(
    'name' => 'admin-theme',
    'elements' => array(
        'logo' => array(
            'label' => t('Logo'),
            'type' => 'image',
            'path' => UPLOADS . DS . 'theme' . DS . 'logo',
            'rename' => 'logo',
        ),
        'favicon' => array(
            'label' => t('Icon'),
            'type' => 'image',
            'path' => UPLOADS . DS . 'theme' . DS . 'icon',
            'rename' => 'favicon',
            'allowed_types' => array('ico'),
        ),
        'send' => array(
            'type' => 'submit',
            'label' => t('Update'),
        )
    )
);