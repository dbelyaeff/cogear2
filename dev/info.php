<?php

return array (
  'name' =>t('Шестерёнка'),
  'description' => t('Описание шестерёнки.'),
  'package' => t('Пакет шестерёнок'),
  'order' => 0,
  'required' => array(
      'gears' => array(
          array(
              'name' => 'User',
              'disabled' => TRUE,
              'version' => '1.2',
              'condition' => ' >= ',
          )
      )
  )

);