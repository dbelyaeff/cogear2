<?php

return array(
    'name' => t('Права доступа'),
    'description' => t('Управления правами доступа.'),
    'package' => t('Права доступа'),
    'order' => '-998',
    'required' =>
    array(
        'gears' =>
        array(
            0 =>
            array(
                'name' => 'User',
            ),
        ),
    ),
);