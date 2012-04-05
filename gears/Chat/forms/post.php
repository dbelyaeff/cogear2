<?php
return array(
       'name' => 'chat',
        'elements' => array(
            'body' => array(
                'type' => 'text',
                'validators' => array('Required',array('Length','3'))
            ),
            'post' => array(
                'type' => 'submit',
                'label' => t('Post'),
            )
        )
);