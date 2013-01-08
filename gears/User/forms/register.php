<?php

return array(
    'name' => 'user-register',
    'elements' => array(
        'email' => array(
            'label' => t('Электронная почта'),
            'type' => 'text',
            'placeholder' => t('Укажите адрес электронной почты…'),
            'validators' => array('Email', 'Required', 'User_Validate_EmailReg'),
        ),
        'actions' => array(
            'elements' => array(
                'submit' => array(
                    'label' => t('Зарегистрироваться'),
                ),
            )
        )
    )
);