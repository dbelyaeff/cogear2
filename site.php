<?php

return array(
    'database' =>
    array(
        'driver' => 'Db_Driver_PDO_Mysql',
        'host' => 'localhost',
        'base' => 'cogear',
        'user' => 'root',
        'pass' => NULL,
        'port' => NULL,
        'prefix' => NULL,
    ),
    'gears' =>
    array('Access', 'Admin', 'Ajax', 'Assets', 'Bootstrap', 'Breadcrumb', 'Cache', 'DateTime', 'Db', 'Dev', 'Errors', 'Fancybox', 'File', 'Form', 'Gears', 'Geo','Lang', 'Icons', 'Image', 'Input', 'Jevix', 'Log', 'Mail', 'Menu', 'Meta', 'Modal', 'Notify', 'Pager','Request', 'Response', 'Roles', 'Router', 'Secure', 'Session', 'Table', 'Template', 'Theme', 'User', 'Zip', 'jQuery'),
    'permitted_uri_chars' => 'а-яa-z0-9\s_\.\-',
    'key' => 'ba96917974845ff8a6f178c9551c17c7',
    'site' =>
    array(
        'url' => 'cogear.new',
    ),
    'mail' =>
    array(
        'from' => 'admin@cogear.ru',
        'from_name' => 'cogear',
        'signature' => '<p>-----------------------<br/>С уважением,<br/>почтально сайта http://cogear.ru',
    ),
);