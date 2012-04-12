<div class="user_profile">
    <div class="user_profile_navbar">
        <?php
        $navbar = new Stack(array('name' => 'user.profile.navbar'));
        $navbar->attach($user);
        $navbar->avatar = $user->getAvatarImage('avatar.profile');
        $navbar->name = '<strong>' . $user->getLink() . '</strong>';
        if (access('user.edit_all') OR $user->id == cogear()->user->id) {
            $navbar->edit = '<a href="' . l('/user/edit/' . $user->id) . '" class="btn btn-primary btn-mini">' . t('Edit') . '</a>';
        }
        echo $navbar->render();
        ?>
    </div>
    <?php
    $tabs = new Menu_Auto(array(
                'name' => 'user.profile.tabs',
                'template' => 'Twitter_Bootstrap.tabs',
                'render' => FALSE,
                'elements' => array(
                    'profile' => array(
                        'label' => t('Profile', 'User'),
                        'link' => $user->getProfileLink(),
                        'active' => cogear()->router->check('user',Router::BOTH),
                    ),
                    'edit' => array(
                        'label' => t('Edit'),
                        'link' => l('/user/edit/' . $user->id),
                        'access' => cogear()->router->check('user/edit'),
                    ),
                ),
            ));
    echo $tabs->render();
    ?>
    
</div>