<?php
return array(
    'name' => 'i18n-admin',
    'elements' => array(
      'title' => array(
          'type' => 'div',
          'class' => 'page-header',
          'label' => '<h1>'.t('Language settings','I18n.admin').'</h1>',
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
          'class' => 'btn btn-primary',
          'label' => t('Save')
      )
    ),
);