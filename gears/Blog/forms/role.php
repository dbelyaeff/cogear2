<?php

return array(
    'name' => 'blog-role',
    'elements' => array(
        'title' => array(
            'label' => t('Change user role', 'Blog'),
        ),
        'role' => array(
            'type' => 'select',
        ),
        'actions' => array(
            'elements' => array(
                'submit' => array(
                    'label' => t('Change', 'Form'),
                )
            )
        )
    ),
);