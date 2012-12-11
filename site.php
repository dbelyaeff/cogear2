<?php
return array (
   // Настройки подключения к базе данных
  'database' =>
  array (
    'driver' => 'Db_Driver_PDO_Mysql',
    'host' => 'localhost',
    'base' => 'cogear',
    'user' => 'root',
    'pass' => '',
    'port' => '3306',
    'prefix' => '',

  ),
  // Включенные по умолчанию шестерёнки
  // Не рекомендуется менять значение этого элемента
  'gears' => array('Access','Admin','Ajax','Assets','Bootstrap','Breadcrumb','Cache','DateTime','Db','Dev','Errors','File','Form','Gears','I18n','Icons','Image','Input','Jevix','Mail','Menu','Meta','Modal','Notify','Pager','Post','Request','Response','Router','Roles','Secure','Session','Table','Template','Theme','User','Zip','jQuery'),
  // Разрешенные в uri символы
  'permitted_uri_chars' => 'а-яa-z0-9\s_\.\-',
  // Уникальный ключ сайта для генерации хешей системы защиты
  'key' => 'ba96917974845ff8a6f178c9551c17c7',
  // Основные параметры сайта
  'site' =>
  array (
    // Базовое название сайта для вывода в заголовке
    'name' => 'cogear',
    // Режим разработки
    'development' => 1,
    // Адрес сайта без http:// и www
    'url' => 'cogear.new',
  ),
  // Параметры элемтроной почты
  'mail' =>
  array (
    'from' => 'admin@cogear.ru',
    'from_name' => 'cogear',
    'signature' => '<p>-----------------------<br/>С уважением,<br/>почтально сайта http://cogear.ru',
  ),
);