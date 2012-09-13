<?php
$i = 0;
foreach ($steps as $key=>$step):
    ?>
    <div class="converter-step" data-key="<?php echo $i ?>" data-id="<?php echo $key ?>" data-source="/admin/converter/adapter/<?php echo $key?>/">
        <div class="page-header">
            <h1>
                <?php echo ($i + 1) . '. ' . t($step, 'Converter') ?>
            </h1>
        </div>
        <div class="alert alert-info">

        </div>
        <button data-action="start" data-target="<?php echo $key?>" class="btn btn-primary"><i class="icon icon-white icon-play"></i> <?php echo t('Start', 'Converter') ?></button>
        <button data-action="progress" data-target="<?php echo $key?>" class="btn"><i class="icon icon-refresh"></i> <?php echo t('In progress…', 'Converter') ?></button>
        <button data-action="success" data-target="<?php echo $key?>" class="btn btn-success"><i class="icon icon-white icon-ok"></i> <?php echo t('Success', 'Converter') ?></button>
        <button data-action="reset" data-target="<?php echo $key?>" class="btn"><i class="icon icon-remove"></i> <?php echo t('Reset', 'Converter') ?></button>
    </div>
    <?php
    $i++;
endforeach;
?>
<div class="t_c m20" id="converter-finish">
    <a href="<?php echo l('/admin/converter/finish') ?>" class="btn btn-primary btn-large"><?php echo t('Finish', 'Converter'); ?></a>
</div>