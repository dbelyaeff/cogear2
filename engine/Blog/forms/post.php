<?php

return array(
    'name' => 'blog-add',
    'elements' => array(
        'name' => array(
            'type' => 'input',
            'label' => t('Title', 'Blog'),
            'class' => 'span6',
            'validators' => array('Required', array('Length', 5)),
        ),
        'body' => array(
            'type' => 'editor',
            'label' => t('Content', 'Blog'),
            'class' => 'span6',
            'validators' => array('Required', array('Length', 5)),
        ),
        'front' => array(
            'type' => 'checkbox',
            'access' => access('Blog.front'),
            'label' => t('Promote to front page'),
        ),
        'preview-holder' => array(
            'type' => 'group',
            'class' => 'btn-group',
            'elements' => array(
                'preview' => array(
                    'type' => 'submit',
                    'class' => 'btn',
                    'label' => t('Preview', 'Blog'),
                ),
            ),
        ),
        'buttons' => array(
            'type' => 'group',
            'class' => 'btn-group',
            'elements' => array(
                'save' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-success',
                    'label' => t('Save', 'Blog'),
                ),
                'publish' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                    'label' => t('Publish', 'Blog'),
                ),
            )
        ),
        'delete' => array(
            'type' => 'delete',
            'class' => 'fl_r',
            'label' => t('Delete'),
        ),
    )
);