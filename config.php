<?php
return array (
  'cache' =>
  array (
    'driver' => 'Cache_Driver_File',
    'path' => CACHE.'',
    'prefix' => 'normal',
    'enabled' => true,
    'name' => 'normal',
  ),
  'development' => 1,
  'router' =>
  array (
    'defaults' =>
    array (
      'gear' => 'Admin',
      'action' => 'index_action',
    ),
  ),
  'assets' =>
  array (
    'js' =>
    array (
      'glue' => true,
      'filename' => 'scripts.js',
      'driver' => 'Assets_Driver_JS',
      'render' => 'head',
      'refresh' => 0,
      'name' => 'scripts',
    ),
    'css' =>
    array (
      'glue' => true,
      'filename' => 'styles.css',
      'driver' => 'Assets_Driver_CSS',
      'render' => 'head',
      'refresh' => 0,
      'name' => 'styles',
    ),
  ),
  'gears' =>
  array (
      'Install'
  ),
  'lang' =>
  array (
    'lang' => 'ru',
    'locale' => 'ru_RU',
    'path' => SITE.'/lang',
    'driver' => 'Lang_Driver_File',
    'name' => 'index',
    'ignore_native' => true,
    'available' =>
    array (
      0 => 'ru',
      1 => 'en',
    ),
  ),
  'theme' =>
  array (
    'current' => 'Default',
  ),
  'cron' =>
  array (
    'last_run' => 1351775236,
  ),
  'user' =>
  array (
    'refresh' => 600,
    'register' =>
    array (
      'active' => false,
      'verification' => true,
    ),
    'avatar' =>
    array (
      'default' => 'avatars/0/avatar.jpg',
    ),
    'last_visit' => 1347460375,
  ),
  'image' =>
  array (
    'presets' =>
    array (
      'blog' =>
      array (
        'avatar' =>
        array (
          0 => 'resize(128, 128, crop)',
        ),
        'profile' =>
        array (
          0 => 'resize(64, 64, crop)',
        ),
        'small' =>
        array (
          0 => 'resize(32, 32, crop)',
        ),
        'tiny' =>
        array (
          0 => 'resize(24, 24, crop)',
        ),
      ),
      'post' =>
      array (
        'large' =>
        array (
          0 => 'resize(700, 500, height, down)',
        ),
      ),
      'image' =>
      array (
        'thumb' =>
        array (
          0 => 'resize(130,130, width)',
        ),
        'small' =>
        array (
          0 => 'resize(200, 100, height, down)',
        ),
        'medium' =>
        array (
          0 => 'resize(500, 300, height, down)',
        ),
        'large' =>
        array (
          0 => 'resize(800, 600, height, down)',
        ),
      ),
      'avatar' =>
      array (
        'tiny' =>
        array (
          0 => 'resize(16, 16, crop)',
        ),
        'small' =>
        array (
          0 => 'resize(24, 24, crop)',
        ),
        'medium' =>
        array (
          0 => 'resize(24, 24, crop)',
        ),
        'large' =>
        array (
          0 => 'resize(64, 64, crop)',
        ),
        'photo' =>
        array (
          0 => 'resize(200, 200, width)',
        ),
      ),
    ),
  ),
  'site' =>
  array (
    'name' => 'Мой тестовый сайт',
  ),
  'wysiwyg' =>
  array (
    'editor' => 'redactor',
  ),
  'Pages' =>
  array (
    'main_id' => '1',
  ),
);