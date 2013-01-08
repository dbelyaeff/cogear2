<?php

return array(
    'name' =>t('Базы данных'),
    'description' => t('Работа с базами данных.'),
    'order' => '-9999',
    'required' =>
    array(
        'gears' =>
        array(
            0 =>
            array(
                'name' => 'Install',
                'disabled' => 'TRUE',
            ),
        ),
    ),
);