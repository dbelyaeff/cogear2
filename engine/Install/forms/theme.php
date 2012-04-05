<?php

return array(
    'name' => 'install-theme',
    'elements' => array(
        'theme' => array(
            'type' => 'select',
            'label' => t('Choose theme'),
            'value' => config('theme.current'),
        ),
        'save' => array(
            'type' => 'submit',
            'label' => t('Next')
        )
    )
);