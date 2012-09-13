<?php

return array(
    'name' => array(
        'label' => t('Title', 'Form.elements'),
        'type' => 'text',
        'validators' => array('Required', array('Length', 3)),
        'class' => 'ajaxed',
    ),
    'link' => array(
        'type' => 'text',
        'label' => t('Link', 'Form.elements'),
        'validators' => array(array('Length', 3)),
        'filters' => array('Uri'),
        'description' => t('This string will be used to form link. If empty field will be filled automatically.', 'Form.descriptions'),
    ),
    'body' => array(
        'type' => 'editor',
        'label' => t('Text', 'Form.elements'),
        'filters' => array('Jevix_Filter'),
        'validators' => array('Required', array('Length', 1)),
    ),
    'description' => array(
        'type' => 'editor',
        'label' => t('Description', 'Form.elements'),
        'filters' => array('Jevix_Filter'),
        'validators' => array('Required', array('Length', 1)),
    ),
    'actions' => array(
        'type' => 'group',
        'class' => 'form-actions',
    ),
    'delete' => array(
        'type' => 'delete',
        'class' => 'fl_r',
        'label' => t('Delete')
    ),
    'preview' => array(
        'type' => 'submit',
        'class' => 'btn',
        'label' => t('Preview', 'Form.elements'),
    ),
    'draft' => array(
        'type' => 'submit',
        'class' => 'btn btn-success',
        'label' => t('Draft', 'Form.elements'),
    ),
    'publish' => array(
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'label' => t('Publish', 'Form.elements'),
    ),
    'buttons' => array(
        'type' => 'group',
        'class' => 'btn-group',
    ),
    'title' => array(
        'type' => 'title',
    ),
    'submit' => array(
        'type' => 'submit',
        'label' => t('Send'),
        'class' => 'btn btn-primary',
    ),
);