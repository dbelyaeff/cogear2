<?php
return array(
    'database' => array(
        'dsn' => 'mysql://root@localhost/cogear',
    ),
    'permitted_uri_chars' => 'а-яa-z0-9\s_\.',
    'cache' => array(
        'adapter' => 'Cache_Adapter_File',
        'enabled' => TRUE,
        'path' => SITE . DS . 'cache',
    ),
    'session' => array(
        'adapter' => 'Session_Adapter_File',
        'enabled' => TRUE,
        'path' => SITE.DS.'cache'.DS.'sessions',
        'use_cookies' => 'on',
        'session_expire' => 86400,
    ),
    // Secret key
    'key' => 'asdlmk1;2u3192y23uhsdhhabgkjahsdgkjghasd',
    'development' => TRUE,
    'site' => array(
        'name' => 'cogear',
    ),
    'i18n' => array(
        'adapter' => 'I18n_Adapter_File',
        'path' => SITE.DS.'lang',
    ),
);

