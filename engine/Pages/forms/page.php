<?php

return array(
    'name' => 'page',
    'elements' => array(
        'title' => array(
            'type' => 'fieldset',
            'label' => t('Create page','Pages'),
            'elements' => array(
                'name' => array(
                    'type' => 'text',
                    'label' => t('Title', 'Pages'),
                    'validators' => array('Required', array('Length', 5)),
                ),
                'pid' => array(
                    'type' => 'select',
                    'label' => t('Parent page', 'Pages'),
                    'callback' => 'Pages->getFormSelect',
                ),
                'body' => array(
                    'type' => 'editor',
                    'label' => t('Content', 'Pages'),
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
                                    'label' => t('Preview', 'Pages'),
                                ),
                                'draft' => array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-success',
                                    'label' => t('Draft', 'Pages'),
                                ),
                                'publish' => array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary',
                                    'label' => t('Publish', 'Pages'),
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