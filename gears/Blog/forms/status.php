<?php
return array(
    'name' => 'blog-status',
    'elements' => array(
        'title' => array(
            'label' => '',
            'type' => 'fieldset',
            'elements' => array(
                'body' => array(
                    'type' => 'div',
                    'label' => '',
                ),
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