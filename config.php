<?php 
return array (
  'i18n' => 
  array (
    'lang' => 'ru',
    'locale' => 'ru_RU.UTF8',
  ),
  'theme' => 
  array (
    'logo' => '/theme/logo/logo.png',
    'favicon' => '/theme/icon/favicon.ico',
    'current' => 'Default',
  ),
  'cron' => 
  array (
    'last_run' => 1314449280,
  ),
  'user' => 
  array (
    'avatar' => array(
        'default' => 'avatars/0/avatar.jpg',
    )
  ),
  'image' => 
  array (
    'presets' => 
    array (
      'post' => 
      array (
        'size' => '150x150',
        'actions' => 
        array (
          0 => 'resize',
        ),
      ),
      'avatar' => 
      array (
        'navbar' => array(
            'size' => '41x41',
            'actions' => array(
                'sizecrop',
            )
        ),
        'small' => 
        array (
          'size' => '24x24',
          'actions' => 
          array (
            0 => 'sizecrop',
          ),
        ),
        'profile' => 
        array (
          'size' => '32x32',
          'actions' => 
          array (
            0 => 'sizecrop',
          ),
        ),
        'photo' => 
        array (
          'size' => '200x200',
          'actions' => 
          array (
            0 => 'resize',
          ),
        ),
      ),
    ),
  ),
  'installed' => true,
);