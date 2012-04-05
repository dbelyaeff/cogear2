<?php
return array(
            'name' => 'user-login',
            'elements' => array(
                'login' => array(
                    'label' => t('Login','User'),
                    'type' => 'text',
                    'validators' => array(array('Length',3),'AlphaNum','Required','User_Validate_Login'),
                ),
                'email' => array(
                    'label' => t('E-Mail','User'),
                    'type' => 'text',
                    'validators' => array('Email','Required','User_Validate_Email'),
                ),
                'password' => array(
                    'label' => t('Password','User'),
                    'type' => 'password',
                    'validators' => array(array('Length',3),'AlphaNum','Required')
                ),
//                'role' => array(
//                    'label' => t('Role','User'),
//                    'type' => 'select',
//                    'validators' => array('Required'),
//                    'callback' => 'User_Gear->getRoles',
//                ),
//                'options' => array(
//                    'label' => t('Test','User'),
//                    'type' => 'checkbox',
//                ),
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Register'),
                )

            )
        );