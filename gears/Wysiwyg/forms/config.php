<?php
return array(
    'name' => 'editor-config',
    'title' => t('Wysiwyg settings','Wysiwyg'),
    'elements' => array(
        'type' => array(
            'label' => t('Choose an editor:','Wysiwyg'),
            'type' => 'select',
        ),
        'submit' => array(
            'type' => 'submit',
            'label' => t('Save'),
        )
    ),
);