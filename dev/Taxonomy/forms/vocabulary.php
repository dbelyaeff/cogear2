<?php

return array(
    'name' => 'taxonomy-vocabulary',
    'elements' => array(
        'name' => array(),
        'link' => array(),
        'type' => array(
            'type' => 'select',
            'label' => t('Type','Taxonomy'),
            'values' => array(
                0 => t('Private','Taxonomy'),
                1 => t('Public','Taxonomy'),
            )
        ),
        'is_open' => array(
            'type' => 'select',
            'label' => t('Type','Taxonomy'),
            'values' => array(
                0 => t('Closed','Taxonomy'),
                1 => t('Open','Taxonomy'),
            )
        ),
        'is_multiple' => array(
            'type' => 'checkbox',
            'text' => t('Is multichose?','Taxonomy'),
        ),
        'description' => array(),
        'actions' => array(
            'elements' => array(
                'submit' => array('label' => t('Save')),
                'delete' => array(),
            ),
        )
    )
);