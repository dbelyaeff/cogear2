<?php 
return array (
  'cache' => 
  array (
    'driver' => 'Cache_Driver_Memcache',
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
      'gear' => 'Post',
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
    2 => 'Pages',
    3 => 'Wysiwyg',
    4 => 'Markitup',
    5 => 'Cut',
  ),
  'lang' => 
  array (
    'lang' => 'ru',
    'locale' => 'ru_RU',
    'path' => SITE.'/lang',
    'driver' => 'Lang_Driver_File',
    'name' => 'index',
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
    'refresh' => 60,
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
          0 => 'resize(128,128,crop)',
        ),
        'profile' => 
        array (
          0 => 'resize(64,64,crop)',
        ),
        'small' => 
        array (
          0 => 'resize(32,32,crop)',
        ),
        'tiny' => 
        array (
          0 => 'resize(24,24,crop)',
        ),
      ),
      'post' => 
      array (
        'large' => 
        array (
          0 => 'resize(700,500,height,down)',
        ),
      ),
      'avatar' => 
      array (
        'navbar' => 
        array (
          0 => 'resize(36,36,crop)',
        ),
        'comment' => 
        array (
          0 => 'resize(28,28,crop)',
        ),
        'small' => 
        array (
          0 => 'resize(24,24,crop)',
        ),
        'tiny' => 
        array (
          0 => 'resize(16,16,crop)',
        ),
        'post' => 
        array (
          0 => 'resize(24,24,crop)',
        ),
        'profile' => 
        array (
          0 => 'resize(64,64,crop)',
        ),
        'photo' => 
        array (
          0 => 'resize(200,200,width)',
        ),
      ),
    ),
  ),
  'site' => 
  array (
    'name' => 'cogear',
  ),
  'wysiwyg' => 
  array (
    'editor' => 'markitup',
  ),
  'Pages' => 
  array (
    'main_id' => '1',
  ),
);