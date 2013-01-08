<?php

return array(
    'name' => 'themes.add',
    'class' => 'form form-horizontal',
    'elements' => array(
        'info' => array(
            'type' => 'div',
            'class' => 'alert alert-info',
            'label' => t('Вы можете загрузить одну или несколько тем в одном архиве. Система автоматически установит их.'),
        ),
        'field' => array(
            'type' => 'fieldset',
            'elements' => array(
                'file' => array(
                    'label' => t('С диска'),
                    'type' => 'file',
                    'allowed_types' => array('zip'),
                    'maxsize' => 3072,
                    'path' => UPLOADS . DS . 'themes',
                    'overwrite' => TRUE,
                ),
                'or' => array(
                    'type' => 'div',
                    'value' => '<h2>'.t('или').'</h2>',
                ),
                'url' => array(
                    'type' => 'file_url',
                    'label' => t('С Интернета'),
                    'class' => 'input-xxxlarge',
                    'allowed_types' => array('zip'),
                    'maxsize' => 3072,
                    'path' => UPLOADS . DS . 'themes',
                    'overwrite' => TRUE,
                    'validators' => array('Url'),
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