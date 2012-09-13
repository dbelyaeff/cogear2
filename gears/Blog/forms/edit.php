<?php

return array(
    'name' => 'Blog',
    'elements' => array(
        'title' => array(
            'type' => 'fieldset',
            'label' => t('Create Blog', 'Blog'),
            'elements' => array(
                'name' => array(
                ),
                'login' => array(
                    'label' => t('Login', 'Blog'),
                ),
                'avatar' => array(
                    'label' => t('Avatar', 'Blog'),
                    'type' => 'image',
                    'preset' => 'blog.avatar',
                    'path' => UPLOADS . DS . 'blogs',
                    'overwrite' => TRUE,
                ),
                'body' => array(
                    'label' => t('Description', 'Blog'),
                ),
                'actions' => array(
                    'elements' => array(
                        'buttons' => array(
                            'type' => 'group',
                            'class' => 'btn-group',
                            'elements' => array(
                                'create' => array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary',
                                    'label' => t('Create', 'Blog'),
                                ),
                            ),
                        ),
                    )
                ),
            ),
        ),
    ),
);