<?php

return array(
    'name' =>t('Пользователи'),
    'description' => t('Управления пользователями.'),
    'order' => '-999',
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