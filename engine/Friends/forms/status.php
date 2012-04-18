<?php
return array(
    'name' => 'friends-status',
    'elements' => array(
        'title' => array(
            'label' => '',
            'type' => 'fieldset',
            'elements' => array(
                'actions' => array(
                    'type' => 'group',
                    'class' => 'form-actions',
                    'elements' => array(
                        'yes' => array(
                            'label' => t('Yes'),
                            'type' => 'submit',
                            'class' => 'btn btn-primary',
                        ),
                        'no' => array(
                            'label' => t('No'),
                            'type' => 'submit',
                            'class' => 'btn btn-danger',
                        ),
                    )
                ),
            ), 
        ),
    )
);