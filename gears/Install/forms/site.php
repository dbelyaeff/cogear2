<?php

return array(
    'name' => 'install',
    'elements' => array(
        'sitename' => array(
            'type' => 'text',
            'label' => t('Название сайта'),
            'validators' => array('Required'),
            'value' => config('site.name'),
        ),
        'database' => array(
            'type' => 'text',
            'label' => t('Настройка соединения с базой данных'),
            'placeholder' => 'mysql://root:password@localhost/database',
            'description' => t('Пример: <b>mysql://root:password@localhost/database</b>.'),
            'validators' => array('Db_Validate_DSN','Required'),
            'value' => 'mysql://root@localhost/cogear',
        ),
        'create_db' => array(
            'type' => 'checkbox',
            'label' => t('Попытаться создать базу данных'),
        ),
        'save' => array(
            'type' => 'submit',
            'label' => t('Далее'),
            'class' => 'btn btn-primary',
        )
    )
);