<?php
return array(
    'name' => 'admin-site',
    'elements'=> array(
        'title' => array(
            'label' => t('Настройки'),
        ),
        'name' => array(
            'type' => 'text',
            'label' => t('Название сайта'),
            'validators' => array('Required'),
        ),
        'dev' => array(
            'type' => 'checkbox',
            'label' => t('Режим разработки'),
            'value' => config('site.development'),
        ),
        'save' => array(
        )
    )
);