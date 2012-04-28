<?php

return array(
    'name' => 'comment',
    'action' => l('/comments/post/'),
    'elements' => array(
        'title' => array(
            'type' => 'title',
            'label' => t('Post new comment %s','Comments',cogear()->user->getAvatarLinked().' '.cogear()->user->getProfileLink()),
        ),
        'body' => array(
            'type' => 'editor',
            'validators' => array('Required',array('Length',5)),
            'editor' => array(
                'toolbar' => 'comment',
            ),
        ),
        'actions' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
                'preview' => array(
                    'type' => 'submit',
                    'class' => 'btn',
                    'label' => t('Preview'),
                ),
                'publish' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                    'label' => t('Publish'),
                ),
                'delete' => array(
                    'type' => 'delete',
                    'class' => 'fl_r',
                    'label' => t('Delete'),
                ),
            ),
        ),

    )
);