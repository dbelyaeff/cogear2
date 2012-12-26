<?php

return array(
    'name' => 'user-lostpassword',
    'elements' => array(
        'login' => array(
            'label' => t('Логин или элетропочта'),
            'placeholder' => t('Укажите имя пользователя или адрес электронной почты…'),
            'type' => 'text',
            'validators' => array('Required'),
        ),
        'actions' => array(
            'elements' => array(
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Сбросить пароль'),
                    'class' => 'btn btn-primary',
                )
            ),
        ),
    )
);