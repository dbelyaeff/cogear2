<?php

return array (
  'name' => 'Шестерёнка',
  'description' => 'Описание шестерёнки.',
  'package' => 'Пакет шестерёнок',
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