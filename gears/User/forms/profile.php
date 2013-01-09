<?php

return array(
    '#name' => 'user.profile',
    'personal' => array(
        '#label' => t('Настройки'),
        '#type' => 'fieldset',
        'avatar' => array(
            '#label' => t('Аватар', 'User'),
            '#type' => 'image',
            '#preset' => 'avatar.photo',
            '#maxsize' => '300Kb',
            '#path' => UPLOADS . DS . 'avatars' . DS . cogear()->user->id,
            '#overwrite' => TRUE,
            '#rename' => cogear()->user->id,
        ),
        'name' => array(
            '#label' => t('Настоящее имя'),
            '#type' => 'text',
            '#validators' => array(array('Length', 3, 30), 'Name'),
        ),
        'login' => array(
            '#label' => t('Имя пользователя'),
            '#type' => 'text',
            '#access' => 'User.edit.login',
            '#validators' => array(array('Length', 3), 'AlphaNum', 'Required', array('User_Validate_Login', User_Validate_Login::EXCLUDE_SELF)),
        ),
        'email' => array(
            '#label' => t('Электронная почта'),
            '#type' => 'text',
            '#validators' => array('Email', 'Required', array('User_Validate_Email', User_Validate_Email::EXCLUDE_SELF)),
            '#access' => 'User.edit.email',
        ),
        'password' => array(
            '#label' => t('Пароль'),
            '#type' => 'password',
            '#validators' => array(array('Length', 3), 'AlphaNum')
        ),
    ),
    'submit' => array(
        '#label' => t('Сохранить'),
    ),
    'delete' => array(
    )
);