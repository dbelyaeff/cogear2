<?php

return array(
    'name' => 'image-upload',
    'ajax' => TRUE,
    'class' => 'horizontal',
    'elements' => array(
        'title' => array(
            'label' => '<h1>'.t('Загрузка изображений').'</h1>'
        ),
        'image' => array(
            'type' => 'image',
            'label' => t('Выберите файл с диска'),
            'maxsize' => '100Kb',
        ),
        'dragndrop' => array(
            'type' => 'div',
            'class' => 'well t_c',
            'label' => t('или<p><b>Перетащите его мышкой на это поле.</b>'),
        ),
    ),
);