<?php

return array(
    'router' => array(
        'index' => 'Post',
    ),
    'assets' => array(
        'js' => array(
            'glue' => TRUE,
            'filename' => 'scripts.js',
            'driver' => 'Assets_Driver_JS',
            'render' => 'head',
            'refresh' => 60,
        ),
        'css' => array(
            'glue' => TRUE,
            'filename' => 'styles.css',
            'driver' => 'Assets_Driver_CSS',
            'render' => 'head',
            'refresh' => 60,
        ),
    ),
    'gears' =>
    array(
        'Install'
    ),
    'i18n' =>
    array(
        'lang' => 'ru',
        'locale' => 'ru_RU.UTF-8',
    ),
    'theme' =>
    array(
        'logo' => '/theme/logo/logo.png',
        'favicon' => '/theme/icon/favicon.ico',
        'current' => 'Default',
    ),
    'cron' =>
    array(
        'last_run' => 1351775236,
    ),
    'user' =>
    array(
        'refresh' => 60,
        'register' =>
        array(
            'verification' => true,
        ),
        'avatar' =>
        array(
            'default' => 'avatars/0/avatar.jpg',
        ),
        'last_visit' => 1347460375,
    ),
    'image' =>
    array(
        'presets' =>
        array(
            'blog' =>
            array(
                'avatar' =>
                array(
                    0 => 'resize(128,128,crop)',
                ),
                'profile' =>
                array(
                    0 => 'resize(64,64,crop)',
                ),
                'small' =>
                array(
                    0 => 'resize(32,32,crop)',
                ),
                'tiny' =>
                array(
                    0 => 'resize(24,24,crop)',
                ),
            ),
            'post' =>
            array(
                'large' =>
                array(
                    0 => 'resize(700,500,height,down)',
                ),
            ),
            'avatar' =>
            array(
                'navbar' =>
                array(
                    0 => 'resize(36,36,crop)',
                ),
                'comment' =>
                array(
                    0 => 'resize(28,28,crop)',
                ),
                'small' =>
                array(
                    0 => 'resize(24,24,crop)',
                ),
                'tiny' =>
                array(
                    0 => 'resize(16,16,crop)',
                ),
                'post' =>
                array(
                    0 => 'resize(24,24,crop)',
                ),
                'profile' =>
                array(
                    0 => 'resize(64,64,crop)',
                ),
                'photo' =>
                array(
                    0 => 'resize(200,200,width)',
                ),
            ),
        ),
    ),
    'Pages' =>
    array(
        'root_link' => true,
    ),
    'ReCaptcha' =>
    array(
        'public' => '6Lc2s9ESAAAAACmHbFjk5VUf_IUd7Srum4K2KTRo',
        'private' => '6Lc2s9ESAAAAALSiny44S4M3Q0tWH-WxnKrvQQrd',
        'api_server' => 'http://www.google.com/recaptcha/api',
        'api_secure_server' => 'https://www.google.com/recaptcha/api',
        'verify_server' => 'www.google.com',
        'signup_url' => 'https://www.google.com/recaptcha/admin/create',
        'theme' => 'clean',
    ),
    'friends' =>
    array(
    ),
    '/users/reset/9' => true,
    '/users/reset/1' => true,
    'users' =>
    array(
        'reset' =>
        array(
            1 => true,
        ),
    ),
    'front' =>
    array(
        'counters' =>
        array(
            'all' =>
            array(
                'all' => '5',
                'best' => '0',
                'new' => '0',
            ),
            'blogs' =>
            array(
                'all' => '1',
                'best' => '0',
                'new' => '0',
            ),
            'users' =>
            array(
                'all' => '4',
                'best' => '0',
                'new' => '0',
            ),
        ),
    ),
    'votes' =>
    array(
    ),
    'wysiwyg' =>
    array(
        'editor' => 'redactor',
    ),
);