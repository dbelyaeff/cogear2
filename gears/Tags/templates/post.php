<div class="post-tags">
    <i class="icon icon-tags" title="<?php echo t('Tags','Tags');?>"></i>
    <?php $i = 0;
    foreach($tags as $tag):?>
        <a href="<?php echo l('/tags/'.$tag);?>"><?php echo $tag;?></a><?php if($i < sizeof($tags) - 1){ echo ', ';} $i++;?>
    <?php endforeach;?>
</div>