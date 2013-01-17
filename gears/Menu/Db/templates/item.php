<div class="dd-container" data-thread="<?php echo ltrim($item->thread) ?>" data-id="<?php echo $item->id ?>" data-pid="<?php echo $item->pid ?>">
    <a class="sh dd-handle"><i class="icon icon-move"></i></a>
    <span><?php echo $item->label ?></span>
    <a target="_blank" href="<?php echo $item->link ?>" class="sh"><i class="icon icon-share-alt"></i></a>
    <a href="<?php echo l('/admin/theme/menu/'.$item->menu_id.'/item/'.$item->id)?>" class="dd-edit sh fl_r"><i class="icon icon-pencil"></i></a>
</div>