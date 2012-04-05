<?php
return array(
    'name' => 'page-createdit',
    'elements' => array(
        'name' => array(
            'type' => 'text',
            'label' => t('Title'),
            'description' => t('Provide title of the page.'),
            'validators' => array(array('Length', 3), 'Required'),
        ),
        'body' => array(
            'type' => 'textarea',
            'label' => t('Text'),
            'validators' => array(array('Length', 10),'Required'),
        ),
        'submit' => array(
            'type' => 'submit',
            'label' => t('Save'),
        )
    ),
);