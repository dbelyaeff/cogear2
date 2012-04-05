<?php
return array(
    'name' => 'i18n-admin',
    'elements' => array(
      'title' => array(
          'type' => 'div',
          'value' => '<h1>'.t('Internacionalization settings','Forms').'</h1>',
      ),
      'lang' => array(
        'type' => 'select',
        'label' => t('Please, choose site interface language:','I18n'),
        'values' => array(
            'en' => 'English',
            'ru' => 'Русский'
        ),
        'value' => config('i18n.lang')
      ),  
      'save' => array(
          'type' => 'submit',
          'label' => t('Save')
      )
    ),
);