<div class="widget <?php echo $item->options->class ?> shd" id="widget-<?php echo $item->id?>" data-id="<?php echo $item->id?>">
    <?php if(access('Widgets')):?>
    <div class="widget-controls">
        <a href="<?php echo l('/widgets/edit/'.$item->id)?>" class="sh"><i class="icon icon-pencil" title="<?php echo t('Edit','Widgets');?>"></i></a>
        <a href="<?php echo l('/widgets/options/'.$item->id)?>" class="sh"><i class="icon icon-cog" title="<?php echo t('options','Widgets');?>"></i></a>
        <a href="<?php echo l('/widgets/remove/'.$item->id)?>" class="sh"><i class="icon icon-remove" title="<?php echo t('Remove','Widgets');?>"></i></a>
    </div>
    <?php endif;?>
    <?php echo $item->code ?>
</div>