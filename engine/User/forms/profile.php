<?php

return array(
    'name' => 'user-profile',
    'elements' => array(
        'personal' => array(
            'label' => t('Personal', 'User'),
            'type' => 'fieldset',
            'elements' => array(
                'avatar' => array(
                    'label' => t('Avatar', 'User'),
                    'type' => 'image',
                    'preset' => 'avatar.photo',
                    'path' => UPLOADS . DS . 'avatars' . DS . cogear()->user->id,
                    'overwrite' => TRUE,
                    'rename' => cogear()->user->id,
                ),
                'name' => array(
                    'label' => t('Real name', 'User'),
                    'type' => 'text',
                    'access' => access('user_edit_realname'),
                    'validators' => array(array('Length', 5), 'Name'),
                ),
                'login' => array(
                    'label' => t('Login', 'User'),
                    'type' => 'text',
                    'access' => access('user edit_login'),
                    'validators' => array(array('Length', 3), 'AlphaNum', 'Required', array('User_Validate_Login', User_Validate_Login::EXCLUDE_SELF)),
                ),
                'email' => array(
                    'label' => t('E-Mail', 'User'),
                    'type' => 'text',
                    'validators' => array('Email', 'Required', array('User_Validate_Email', User_Validate_Email::EXCLUDE_SELF)),
                    'access' => access('user.edit.email'),
                ),
                'password' => array(
                    'label' => t('Password', 'User'),
                    'type' => 'password',
                    'validators' => array(array('Length', 3), 'AlphaNum')
                ),
            ),
        ),
        'submit' => array(
            'type' => 'submit',
            'label' => t('Update'),
            'class' => 'btn btn-primary',
        ),
        'delete' => array(
            'type' => 'delete',
            'label' => t('Delete'),
        )
    )
);