<?php

return array(
    'name' => 'post',
    'elements' => array(
        'title' => array(
            'type' => 'fieldset',
            'label' => t('Create post'),
            'elements' => array(
                'name' => array(
                    'type' => 'text',
                    'label' => t('Title', 'Blog'),
                    'validators' => array('Required', array('Length', 5)),
                ),
                'body' => array(
                    'type' => 'editor',
                    'label' => t('Content', 'Blog'),
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
                                    'label' => t('Preview', 'Blog'),
                                ),
                                'draft' => array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-success',
                                    'label' => t('Draft', 'Blog'),
                                ),
                                'publish' => array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary',
                                    'label' => t('Publish', 'Blog'),
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