<?php

return array(
    'name' => 'post',
    'elements' => array(
        'title' => array(
            'type' => 'title',
            'label' => t('Create post', 'Post'),
        ),
        'name' => array(
            'type' => 'text',
            'label' => t('Title', 'Post'),
            'validators' => array('Required', array('Length', 5)),
        ),
        'body' => array(
            'type' => 'editor',
            'label' => t('Content', 'Post'),
            'validators' => array('Required', array('Length', 5)),
        ),
        'actions' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'buttons' => array(
                    'type' => 'group',
                    'class' => 'btn-group',
                    'elements' => array(
                        'preview' => array(
                            'type' => 'submit',
                            'class' => 'btn',
                            'label' => t('Preview', 'Post'),
                        ),
                        'draft' => array(
                            'type' => 'submit',
                            'class' => 'btn btn-success',
                            'label' => t('Draft', 'Post'),
                        ),
                        'publish' => array(
                            'type' => 'submit',
                            'class' => 'btn btn-primary',
                            'label' => t('Publish', 'Post'),
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
);