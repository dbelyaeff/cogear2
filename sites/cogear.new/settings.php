<?php 
return array (
  'database' => 
  array (
    'dsn' => 'mysql://root@localhost/cogear',
  ),
  'permitted_uri_chars' => 'а-яa-z0-9\s_\.',
  'cache' => 
  array (
    'adapter' => 'Cache_Adapter_File',
    'enabled' => true,
    'path' => SITE.'\cache',
  ),
  'session' => 
  array (
    'adapter' => 'Session_Adapter_File',
    'enabled' => true,
    'path' => SITE.'\cache\sessions',
    'use_cookies' => 'on',
    'session_expire' => 86400,
  ),
  'key' => '1033b0434543dbf417d7e09432959521',
  'development' => true,
  'site' => 
  array (
    'name' => 'cogear',
  ),
  'i18n' => 
  array (
    'adapter' => 'I18n_Adapter_File',
    'path' => SITE.'\lang',
  ),
);