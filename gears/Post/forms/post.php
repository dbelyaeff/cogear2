<?php

return array(
    'name' => 'post',
    'elements' => array(
        'title' => array(
            'label' => t('Создание публиации'),
        ),
        'name' => array(
            'data-source' => l('/post/ajax/name'),
        ),
        'body' => array(
        ),
        'actions' => array(
            'elements' => array(
                'buttons' => array(
                    'elements' => array(
                        'preview' => array(
                        ),
                        'draft' => array(
                        ),
                        'publish' => array(
                        ),
                    ),
                ),
                'delete' => array(
                ),
            )
        ),
    ),
);