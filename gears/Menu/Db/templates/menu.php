<div class="menu shd" id="menu-<?php echo $object->machine_name?>">
    <?php echo $menu->render()?>
    <?php if(access('Menu.*')):?>
    <a class="sh edit" href="<?php echo l('/admin/theme/menu/'.$object->id)?>"><i class="icon icon-pencil"></i></a>
    <?php endif?>
</div>