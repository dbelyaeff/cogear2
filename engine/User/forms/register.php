<?php
return array(
            'name' => 'user-register',
            'class' => 'form-horizontal',
            'elements' => array(
                'email' => array(
                    'label' => t('E-Mail','User'),
                    'type' => 'text',
                    'validators' => array('Email','Required','User_Validate_EmailReg'),
                ),
                'actions' => array(
                    'type' => 'group',
                    'class' => 'form-actions',
                    'elements' => array(
                        'submit' => array(
                            'type' => 'submit',
                            'label' => t('Register'),
                            'class' => 'btn btn-primary',
                        ),
                    )
                )

            )
        );