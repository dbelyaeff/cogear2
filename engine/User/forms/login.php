<?php
return array(
            'name' => 'user-login',
            'elements' => array(
                'login' => array(
                    'label' => t('Login','User'),
                    'type' => 'text',
                    'validators' => array(array('Length',3),'AlphaNum','Required'),
                ),
                'password' => array(
                    'label' => t('Password','User'),
                    'type' => 'password',
                    'validators' => array(array('Length',3),'AlphaNum','Required')
                ),
                'saveme' => array(
                    'label' => t('remember me'),
                    'type' => 'checkbox',
                ),
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Login'),
                )
            )
        );