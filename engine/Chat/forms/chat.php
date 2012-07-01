<?php

return array(
    'name' => 'chat-create',
    'elements' => array(
        'title' => array(
            'label'=>t('Create chat','Chat'),
        ),
        'users[]' => array(
            'type' => 'select',
            'label' => t('Users', 'Chat'),
            'validators' => array('Required'),
            'values' => array(''),
        ),
        'name' => array(
            'type' => 'text',
            'label' => t('Subject', 'Chat'),
            'validators' => array('Required', array('Length', 4)),
        ),
        'body' => array(
            'type' => 'editor',
            'label' => t('First message','Chat'),
            'validators' => array('Required', array('Length', 4)),
        ),
        'actions' => array(
            'elements' => array(
                'send' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                    'label' => t('Send', 'Chat'),
                ),
            ),
        ),
    ),
);