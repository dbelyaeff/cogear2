<?php

return array(
    'database' =>
    array(
        'dsn' => 'mysql://root@localhost/cogear',
    ),
    'permitted_uri_chars' => 'а-яa-z0-9\s_\.',
    'key' => 'ba96917974845ff8a6f178c9551c17c7',
    'site' =>
    array(
        'name' => 'cogear',
        'development' => true,
        'url' => 'cogear.new',
    ),
    'date' =>
    array(
        'format' => 'H:i ← d M Y',
    ),
    'mail' => array(
        'from' => 'admin@cogear.ru',
        'from_name' => 'cogear',
        'signature' => '<p>-----------------------<br/>Best regards<br/>cogear<br/>http://cogear.ru',
        'smtp' => array(
            'login' => 'test@cogear.ru',
            'password' => 'VsIQ4PYPPhdD13um',
            'host' => 'ssl://smtp.gmail.com:465',
        )
    ),
    'installed' => true,
);