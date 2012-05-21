<?php

return array(
    'name' => 'page',
    'elements' => array(
        'title' => array(
            'label' => t('Create page', 'Pages'),
        ),
        'name' => array(
        ),
        'link' => array(
        ),
        'pid' => array(
            'type' => 'select',
            'label' => t('Parent page', 'Pages'),
            'callback' => 'Pages->getFormSelect',
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
            ),
        ),
    ),
);