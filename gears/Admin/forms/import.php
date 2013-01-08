<?php

return array(
    'name' => 'admin.site.import',
    'class' => 'form form-horizontal',
    'elements' => array(
        'field' => array(
            'type' => 'fieldset',
            'elements' => array(
                'file' => array(
                    'type' => 'file',
                    'allowed_types' => array('zip'),
                    'maxsize' => 3072,
                    'path' => UPLOADS . DS . 'config',
                    'overwrite' => TRUE,
                ),
            ),
        ),
        'actions' => array(
            'class' => 't_c',
            'elements' => array(
                'submit' => array(
                    'class' => 'btn btn-primary btn-large',
                    'label' => t('Загрузить'),
                )
            )
        )
    )
);