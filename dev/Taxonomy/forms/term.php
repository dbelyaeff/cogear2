<?php

return array(
    'name' => 'taxonomy-term',
    'elements' => array(
        'name' => array(),
        'link' => array(),
        'vid' => array(
            'type' => 'select',
            'label' => t('Vocabulary','Taxonomy'),
            'values' => cogear()->taxonomy->getVocabularies(),
        ),
        'actions' => array(
            'elements' => array(
                'submit' => array('label' => t('Save')),
                'delete' => array(),
            ),
        )
    )
);