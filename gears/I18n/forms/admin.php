<?php
return array(
    'name' => 'i18n-admin',
    'elements' => array(
      'title' => array(
          'label' => '<h1>'.t('Языковые настройки').'</h1>',
      ),
      'lang' => array(
        'type' => 'select',
        'label' => t('Выберите язык интерфейса'),
        'values' => array(
            'en' => 'English',
            'ru' => 'Русский'
        ),
        'value' => config('i18n.lang')
      ),
      'save' => array(
      )
    ),
);