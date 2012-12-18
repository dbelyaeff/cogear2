<?php

return array(
    'name' => 'theme-choose',
    'elements' => array(
      'title' => array(
          'label' => t('Внеший вид'),
      ),
      'theme' => array(
          'type' => 'select',
          'label' => t('Выберите тему оформления')
      ),
      'actions' => array(
          'elements' => array(
              'submit' => array(),
          )
      ),
    ),
);