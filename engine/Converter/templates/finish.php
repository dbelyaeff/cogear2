<div class="alert alert-success">
    <?php echo t('Your site has been successfully converted!','Converter')?>
</div>
<div class="alert alert-info">
    <?php echo t('You can restart the conversion process or go to the main page.','Converter')?>
</div>
    <p align="m10">
        <a href="<?php echo l()?>" class="btn btn-large btn-primary"><?php echo t('Main page','Converter')?></a>
        <a href="<?php echo l('/admin/converter/clear/')?>" class="btn btn-danger"><?php echo t('Restart','Converter')?></a>
    </p>
