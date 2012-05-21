<?php

return array(
    'name' => 'Blog',
    'elements' => array(
        'title' => array(
            'label' => t('Create Blog', 'Blog'),
        ),
        'name' => array(
        ),
        'login' => array(
            'label' => t('Login', 'Blog'),
            'validators' => array('Required', array('Length', 3)),
        ),
        'type' => array(
            'type' => 'select',
            'label' => t('Type', 'Blog'),
            'value' => 1,
            'values' => array(
                1 => t('Private', 'Blog'),
                2 => t('Public', 'Blog'),
            ),
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
);