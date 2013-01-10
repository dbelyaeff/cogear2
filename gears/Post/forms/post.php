<?php
return array(
    '#name' => 'post',
    'title' => array(
        'label' => t('Создание публикации'),
    ),
    'name' => array(
        'data-source' => l('/post/ajax/name'),
    ),
    'body' => array(
    ),
    'actions' => array(
        '#class' => 'form-actions',
        'buttons' => array(
                '#class' => 'btn-group',
                'preview' => array(
                ),
                'draft' => array(
                ),
                'publish' => array(
                ),
        ),
        'delete' => array(
        ),
    ),
);