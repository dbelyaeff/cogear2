<?php

return array(
    'name' => 'chat-msg',
    'elements' => array(
        'cid' => array(
            'type' => 'hidden',
            'validators' => array('Required','Num'),
        ),
        'body' => array(
            'label' => '',
            'placeholder' => t('Enter message…','Chat'),
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