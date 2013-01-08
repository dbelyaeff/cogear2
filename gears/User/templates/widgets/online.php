<?php; ?>
<h2><?php echo t('Онлайн') ?></h2>
<?php if ($data->counters->users): ?>
<div class="Онлайн-widget-users">
    <b><?php echo t('Пользователи'); ?></b><sup><?php echo $data->counters->users;?></sup>:
    <?php
        $i = 1;
        foreach ($data->users as $user): ?>
        <?php echo $user->getLink('avatar','avatar.tiny').' '.$user->getLink('profile');
            if($i < $data->counters->users){
                echo ' ,';
            }
            $i++;
        ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($data->counters->Боты): ?>
<div class="Онлайн-widget-Боты">
    <b><?php echo t('Боты'); ?></b><sup><?php echo $data->counters->Боты;?></sup>:
    <?php
        $i = 1;
        foreach ($data->Боты as $name=>$info): ?>
        <?php echo ucfirst($name);
            if($i < $data->counters->Боты){
                echo ' ,';
            }
            $i++;
        ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($data->counters->Гости): ?>
<div class="Онлайн-widget-Гости">
    <b><?php echo t('Гости'); ?></b><sup><?php echo $data->counters->Гости;?></sup>
</div>
<?php endif; ?>
