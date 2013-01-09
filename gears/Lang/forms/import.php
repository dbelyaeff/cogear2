<?php

return array(
    '#name' => 'admin.lang.import',
    '#class' => 'form form-horizontal',
    'field' => array(
        '#type' => 'fieldset',
        'file' => array(
            '#type' => 'file',
            '#allowed_types' => array('zip'),
            '#maxsize' => 3072,
            '#path' => UPLOADS . DS . 'lang',
            '#overwrite' => TRUE,
        ),
    ),
    'actions' => array(
        '#class' => 't_c',
        'submit' => array(
            '#class' => 'btn btn-primary btn-large',
            '#label' => t('Загрузить'),
        )
    )
);