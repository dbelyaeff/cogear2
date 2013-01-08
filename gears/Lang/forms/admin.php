<?php
return array(
    'name' => 'lang-admin',
    'elements' => array(
      'title' => array(
          'label' => '<h1>'.t('Настройки').'</h1>',
          'title' => FALSE,
      ),
      'lang' => array(
        'type' => 'select',
        'label' => t('Выберите язык интерфейса'),
        'values' => array(
            
        ),
        'value' => config('lang.lang')
      ),
      'save' => array(
      )
    ),
);