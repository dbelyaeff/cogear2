<?php
return array(
    'name' => 'editor-config',
    'title' => t('Настройки редактора'),
    'elements' => array(
        'type' => array(
            'label' => t('Выберите редактор:'),
            'type' => 'select',
        ),
        'submit' => array(
            'type' => 'submit',
            'label' => t('Сохранить'),
        )
    ),
);