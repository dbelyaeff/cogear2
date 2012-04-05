<?php
return array(
            'name' => 'user-lostpassword',
            'elements' => array(
                'email' => array(
                    'label' => t('E-Mail','User'),
                    'type' => 'text',
                    'validators' => array('Email','User_Validate_Email','Required'),
                ),

                'submit' => array(
                    'type' => 'submit',
                    'label' => t('Renew password'),
                )

            )
        );