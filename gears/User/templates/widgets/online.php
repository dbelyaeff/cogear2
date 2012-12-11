<?php; ?>
<h2><?php echo t('Online') ?></h2>
<?php if ($data->counters->users): ?>
<div class="online-widget-users">
    <b><?php echo t('Users'); ?></b><sup><?php echo $data->counters->users;?></sup>:
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
<?php if ($data->counters->bots): ?>
<div class="online-widget-bots">
    <b><?php echo t('Bots'); ?></b><sup><?php echo $data->counters->bots;?></sup>:
    <?php
        $i = 1;
        foreach ($data->bots as $name=>$info): ?>
        <?php echo ucfirst($name);
            if($i < $data->counters->bots){
                echo ' ,';
            }
            $i++;
        ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($data->counters->guests): ?>
<div class="online-widget-guests">
    <b><?php echo t('Guests'); ?></b><sup><?php echo $data->counters->guests;?></sup>
</div>
<?php endif; ?>
