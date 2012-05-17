<div class="form-horizontal profile_fields">
    <?php
    $fields = new Stack(array('name' => 'blog.profile.fields'));
    $fields->attach($blog);
    $user = new User();
    $user->id = $blog->aid;
    $user->find();
    $fields->append(array(
        'label' => t('Author', 'Blog'),
        'value' => $user->getLink('avatar').' '.$user->getLink('profile'),
    ));
    $blog->body && $fields->append(array(
        'label' => t('Description', 'Blog'),
        'value' => $blog->body,
    ));
    $fields->append(array(
        'label' => t('Posts', 'Blog'),
        'value' => $blog->posts,
    ));
    $fields->append(array(
        'label' => t('Subscribers', 'Blog'),
        'value' => $blog->users.' <a href="'.$blog->getLink().'/users/'.'" class="btn btn-primary btn-mini">'.t('View').'</a>',
    ));
    $fields->append(array(
        'label' => t('Created', 'Blog'),
        'value' => df($blog->reg_date),
    ));
    $fields->init();
    foreach ($fields as $field) {
        ?>
        <div class="control-group">
            <div class="control-label"><?php echo $field['label'] ?></div>
            <div class="controls"><?php echo $field['value'] ?></div>
        </div>
        <?php
    }
    ?>
</div>