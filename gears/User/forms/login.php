<?php

return array(
    'name' => 'user-login',
    'elements' => array(
        'login' => array(
            'label' => t('Имя пользователя или электронная почта'),
            'type' => 'text',
            'validators' => array('Required'),
        ),
        'password' => array(
            'label' => t('Пароль'),
            'type' => 'password',
            'validators' => array(array('Length', 3), 'AlphaNum', 'Required')
        ),
        'saveme' => array(
            'text' => t('запомнить'),
            'type' => 'checkbox',
        ),
        'buttons' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Войти'),
                    'class' => 'btn btn-primary',
                ),
                'lostpassword' => array(
                    'type' => 'link',
                    'label' => t('Забыли пароль?'),
                    'link' => l('/user/lostpassword/'),
                    'class' => 'btn btn-mini',
                ),
            )
        )
    )
);