<?php

return array(
    'name' => 'vote-points',
    'elements' => array(
        'title' => array(
            'label' => t('Add votes to user','Vote'),
        ),
        'votes' => array(
            'type' => 'text',
            'label' => t('Votes count','Vote'),
            'description' => t('How many vote do you want to add?', 'Vote'),
            'validators' => array('Required','Num'),
        ),
        'actions' => array(
            'elements' => array(
                'submit' => array(
                    'label' => t('Add', 'Vote'),
                ),
            )
        ),
    ),
);