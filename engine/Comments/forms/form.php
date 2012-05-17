<?php

return array(
    'name' => 'comment',
    'action' => l('/comments/post/'),
    'ajax' => TRUE,
    'elements' => array(
        'title' => array(
            'type' => 'title',
            'label' => t('Post comment %s','Comments',cogear()->user->getLink('avatar').' '.cogear()->user->getLink('profile')),
        ),
        'pid' => array(
            'type' => 'hidden',
        ),
        'body' => array(
            'type' => 'editor',
//            'validators' => array('Required',array('Length',5)),
            'filters' => array('Jevix'),
            'placeholder' => t('Post comment text here…','Comments'),
        ),
        'actions' => array(
            'type' => 'group',
            'class' => 'form-actions',
            'elements' => array(
//                'user' => array(
//                    'type' => 'div',
//                    'class' => 'avatar',
//                    'label' => cogear()->user->getLink('avatar','avatar.comment').'<div id="comment-top-angle"></div>',
//                ),
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