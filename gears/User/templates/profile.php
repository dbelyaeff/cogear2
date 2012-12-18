<div class="form-horizontal profile_fields">
    <?php
    $fields = new Stack(array('name' => 'user.profile.fields'));
    $fields->object($user);
    $fields->append(array(
        'label' => t('Зарегистрирован'),
        'value' => df($user->reg_date),
    ));
    $fields->append(array(
        'label' => t('Последнее посещение'),
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