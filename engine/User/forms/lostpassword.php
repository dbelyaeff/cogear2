<?php

return array(
    'name' => 'user-lostpassword',
    'class' => 'form-horizontal',
    'elements' => array(
        'login' => array(
            'label' => t('Login or email', 'User'),
            'type' => 'text',
            'validators' => array('Required'),
        ),
        'buttons' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Renew password'),
                    'class' => 'btn btn-primary',
                )
            ),
        ),
    )
);