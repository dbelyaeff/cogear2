<?php

return array(
    'name' => 'user-login',
    'elements' => array(
        'login' => array(
            'label' => t('Login or e-mail', 'User'),
            'type' => 'text',
            'validators' => array('Required'),
        ),
        'password' => array(
            'label' => t('Password', 'User'),
            'type' => 'password',
            'validators' => array(array('Length', 3), 'AlphaNum', 'Required')
        ),
        'saveme' => array(
            'text' => t('remember me'),
            'type' => 'checkbox',
        ),
        'buttons' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Login'),
                    'class' => 'btn btn-primary',
                ),
                'lostpassword' => array(
                    'type' => 'link',
                    'label' => t('Lost password'),
                    'link' => l('/user/lostpassword/'),
                    'class' => 'btn btn-mini',
                ),
            )
        )
    )
);