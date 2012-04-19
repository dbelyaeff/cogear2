<?php
return array(
    'name' => 'admin-site',
    'elements'=> array(
        'title' => array(
            'type' => 'div',
            'class' => 'page-header',
            'label' => '<h1>'.t('Site settings','Admin.site').'</h1>',
        ),
        'name' => array(
            'type' => 'text',
            'label' => t('Site name','Admin.site'),
            'validators' => array('Required'),
        ),
        'url' => array(
            'type' => 'text',
            'label' => t('Site url','Admin.site'),
            'validators' => array('Required','Form_Validate_Url'),
        ),
        'dev' => array(
            'type' => 'checkbox',
            'text' => t('Development mode','Admin.site'),
        ),
        'date_format' => array(
            'type' => 'text',
            'label' => t('Date format','Admin.site'),
            'validators' => array('Required'),
        ),
        'save' => array(
            'type' => 'submit',
            'label' => t('Update'),
            'class' => 'btn btn-primary',
        )
    )
);