<?php

return array(
    'name' => 'install-site',
    'elements' => array(
        'sitename' => array(
            'type' => 'text',
            'label' => t('Site name').' *',
            'validators' => array('Required')
        ),
        'database' => array(
            'type' => 'text',
            'label' => t('Database connection'),
            'description' => t('Example: mysql://root:password@localhost/database.<br/>You can leave this field blank if you don\'t want to use Database gear.'),
            'validators' => array('Db_Validate_DSN'),
            'value' => config('database.dsn'),
        ),
        'save' => array(
            'type' => 'submit',
            'label' => t('Next')
        )
    )
);