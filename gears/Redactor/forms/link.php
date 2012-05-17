<?php

return array(
    'name' => 'redactor-link',
    'ajaxOnly' => TRUE,
    'elements' => array(
        'link' => array(
            'label' => t('Link'),
            'type' => 'text',
        ),
        'actions' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'linked' => array(
                    'label' => t('Link'),
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                ),
                'unlinkd' => array(
                    'label' => t('Unlink'),
                    'type' => 'submit',
                    'class' => 'btn btn-warning',
                ),
            ),
        ),
    ),
);