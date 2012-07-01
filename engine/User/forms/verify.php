<?php

return array(
    'name' => 'user-register-verify',
    'elements' => array(
        'label' => array(
            'type' => 'div',
            'class' => 'page-header',
            'label' => t('Registration'),
        ),
        'email' => array(
            'type' => 'text',
            'label' => t('Email', 'User'),
            'disabled' => TRUE,
        ),
        'login' => array(
            'label' => t('Login', 'User'),
            'type' => 'text',
            'validators' => array(array('Length', 3), 'AlphaNum', 'Required', 'User_Validate_Login'),
        ),
        'name' => array(
            'label' => t('Real name', 'User'),
            'type' => 'text',
            'access' => access('user_edit_realname'),
            'validators' => array(array('Length', 5), 'Name'),
        ),
        'password' => array(
            'label' => t('Password', 'User'),
            'type' => 'text',
            'validators' => array(array('Length', 3), 'AlphaNum', 'Required')
        ),
        'submit' => array(
            'type' => 'submit',
            'label' => t('Save'),
            'class' => 'btn btn-primary',
        )
    )
);