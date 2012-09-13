<?php
return array(
    'name' => 'code',
    'elements' => array(
        'title' => array(
            'type' => 'title',
            'label' => t('Code Editor'),
        ),
        'editor' => array(
            'type' => 'code_editor',
        ),
        'actions' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'insert' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                    'label' => t('Insert','Popup'),
                ),
                'close' => array(
                    'type' => 'link',
                    'link' => 'javascript:window.close();',
                    'class' => 'btn',
                    'label' => t('Close','Popup'),
                )
            ),
        )
    ),
);