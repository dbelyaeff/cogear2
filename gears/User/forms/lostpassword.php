<?php

return array(
    '#name' => 'user.lostpassword',
    'login' => array(
        'label' => t('Логин или элетропочта'),
        'placeholder' => t('Укажите имя пользователя или адрес электронной почты…'),
        'type' => 'text',
        'validators' => array('Required'),
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'submit' => array(
            'label' => t('Сбросить пароль'),
            'class' => 'btn btn-primary',
        )
    ),
);