<div class="dd-container" data-thread="<?php echo ltrim($item->thread) ?>" data-id="<?php echo $item->id ?>" data-pid="<?php echo $item->pid ?>">
    <a class="sh dd-handle"><i class="icon icon-move"></i></a>
    <span><?php echo $item->name ?></span>
    <a href="<?php echo $item->getLink() ?>" class="sh"><i class="icon icon-eye-open"></i></a>
    <a href="<?php echo $item->getLink('edit') ?>" class="dd-edit sh fl_r"><i class="icon icon-pencil"></i></a>
    <a href="<?php echo l('/admin/pages/create/') . '?pid=' . $item->id ?>" class="sh fl_r"><i class="icon icon-plus"></i></a>
</div>