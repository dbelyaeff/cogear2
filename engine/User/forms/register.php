<?php
return array(
            'name' => 'user-register',
            'elements' => array(
                'email' => array(
                    'label' => t('E-Mail','User'),
                    'type' => 'text',
                    'validators' => array('Email','Required','User_Validate_Email'),
                ),
                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Register'),
                )

            )
        );