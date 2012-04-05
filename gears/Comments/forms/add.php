<?php
return array(
    'name' => 'comment',
    'elements' => array(
        'body' => array(
            'type' => 'textarea',
            'validators' => array('Required',array('Length',5)),
            'filters' => array('strip_tags'),
        ),
        'reply' => array(
            'type' => 'hidden',
        ),
        'submit' => array(
            'type' => 'submit',
            'label' => t('Post'),
        ),
        'preview' => array(
            'type' => 'submit',
            'label' => t('Preview'),
        ),
    )
);
