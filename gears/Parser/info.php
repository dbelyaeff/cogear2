<?php

return array(
    'name' => t('Парсер'),
    'description' => t('Фильтрация HTML-кода.'),
    'order' => '0',
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