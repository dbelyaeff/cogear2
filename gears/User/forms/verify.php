<?php

return array(
    '#name' => 'user.register.verify',
    'label' => array(
        '#type' => 'div',
        '#class' => 'page-header',
        '#label' => t('Регистрация'),
    ),
    'email' => array(
        '#type' => 'text',
        '#label' => t('Электронная почта'),
        '#disabled' => TRUE,
    ),
    'login' => array(
        '#label' => t('Имя пользователя'),
        '#type' => 'text',
        '#validators' => array(array('Length', 3), 'AlphaNum', 'Required', 'User_Validate_Login'),
    ),
    'name' => array(
        '#label' => t('Реальное имя'),
        '#type' => 'text',
        '#access' => access('user_edit_realname'),
        '#validators' => array(array('Length', 5), 'Name'),
    ),
    'password' => array(
        '#label' => t('Пароль'),
        '#type' => 'text',
        '#validators' => array(array('Length', 3), 'AlphaNum', 'Required')
    ),
    'save' => array(
    )
);