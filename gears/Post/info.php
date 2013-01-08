<?php

return array(
    'name' =>t('Пост'),
    'description' => t('Служит для публиации и редактирования материалов на сайте.'),
    'order' => '10',
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