<?php

return array(
    'name' => 'image-upload',
    'ajax' => TRUE,
    'class' => 'horizontal',
    'elements' => array(
        'title' => array(
            'type' => 'div',
            'class' => 'page-header',
            'label' => '<h1>'.t('Upload image','Image').'</h1>'
        ),
        'image' => array(
            'type' => 'image',
            'label' => t('Choose from disk','Image'),
            'maxsize' => '100Kb',
        ),
        'dragndrop' => array(
            'type' => 'div',
            'class' => 'well t_c',
            'label' => t('or<p><b>Drag and drop your images here</b>','Image'),
        ),
    ),
);