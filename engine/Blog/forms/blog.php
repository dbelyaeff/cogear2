<?php

return array(
    'name' => 'Blog',
    'elements' => array(
        'title' => array(
            'type' => 'fieldset',
            'label' => t('Create Blog', 'Blog'),
            'elements' => array(
                'name' => array(
                    'type' => 'text',
                    'label' => t('Title', 'Blog'),
                    'validators' => array('Required', array('Length', 5)),
                ),
                'login' => array(
                    'type' => 'text',
                    'label' => t('Login', 'Blog'),
                    'validators' => array('Required', array('Length', 3)),
                ),
                'type' => array(
                    'type' => 'select',
                    'label' => t('Type', 'Blog'),
                    'value' => 1,
                    'values' => array(
                        1 => t('Public', 'Blog'),
                        2 => t('Private', 'Blog'),
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
                    'type' => 'editor',
                    'label' => t('Content', 'Blog'),
                    'validators' => array(array('Length', 5)),
                ),
                'actions' => array(
                    'type' => 'group',
                    'class' => 'form-actions',
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
                        'delete' => array(
                            'type' => 'delete',
                            'class' => 'fl_r',
                            'label' => t('Delete'),
                        ),
                    )
                ),
            ),
        ),
    ),
);