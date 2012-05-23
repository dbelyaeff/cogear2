<?php

return array(
    'name' => 'chat-create',
    'elements' => array(
        'title' => array(
            'label'=>t('Create chat','Chat'),
        ),
        'to' => array(
            'type' => 'text',
            'label' => t('Recipients', 'Chat'),
            'validators' => array('Required'),
        ),
        'subject' => array(
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