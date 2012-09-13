<?php

return array(
    'name' => 'theme-choose',
    'elements' => array(
      'title' => array(
          'label' => t('Choose theme','Theme'),
      ),
      'theme' => array(
          'type' => 'select',
          'label' => t('Theme','Theme')
      ),
      'actions' => array(
          'elements' => array(
              'submit' => array(),
          )
      ),
    ),
);