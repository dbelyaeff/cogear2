<?php

return array(
    'name' => 'install',
    'elements' => array(
        'sitename' => array(
            'type' => 'text',
            'label' => t('Site name').' *',
            'validators' => array('Required'),
            'value' => config('site.name'),
        ),
        'database' => array(
            'type' => 'text',
            'label' => t('Database connection'),
            'description' => t('Example: <b>mysql://root:password@localhost/database</b>.<br/>You can leave this field blank if you don\'t want to use Database gear.'),
            'validators' => array('Db_Validate_DSN'),
            'value' => config('database.dsn'),
        ),
        'save' => array(
            'type' => 'submit',
            'label' => t('Next'),
            'class' => 'btn btn-primary',
        )
    )
);