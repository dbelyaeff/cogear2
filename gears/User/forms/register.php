<?php

return array(
    '#name' => 'user.register',
    'email' => array(
        '#label' => t('Электронная почта'),
        '#type' => 'text',
        '#placeholder' => t('Укажите адрес электронной почты…'),
        '#validators' => array('Email', 'Required', 'User_Validate_EmailReg'),
    ),
    'actions' => array(
        'submit' => array(
            '#label' => t('Зарегистрироваться'),
        ),
    )
);