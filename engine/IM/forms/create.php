<?php

return array(
    'name' => 'im-create',
    'elements' => array(
        'to' => array(
            'type' => 'text',
            'label' => t('Recipients', 'IM'),
            'validators' => array('Required'),
        ),
        'subject' => array(
            'type' => 'text',
            'label' => t('Subject', 'IM'),
            'validators' => array('Required', array('Length', 4)),
        ),
        'body' => array(
            'type' => 'editor',
            'validators' => array('Required', array('Length', 4)),
        ),
        'actions' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'send' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary span2',
                    'label' => t('Send', 'IM'),
                ),
            ),
        ),
    ),
);