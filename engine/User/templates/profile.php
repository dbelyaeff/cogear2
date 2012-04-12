<div class="form-horizontal user_profile_fields">
    <?php
    $fields = new Stack(array('name' => 'user.profile.fields'));
    $fields->attach($user);
    $fields->append(array(
        'label' => t('Registered', 'User'),
        'value' => df($user->reg_date),
    ));
    $fields->append(array(
        'label' => t('Last visit', 'User'),
        'value' => df($user->last_visit),
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