<ul class="list dd-list shd">
    <?php foreach ($items as $item): ?>
        <li id="item-<?php echo $item->id ?>" class="">
            <div class="name sh"><i class="icon-move handler"></i> <a href="<?php echo $item->getLink() ?>"><?php echo $item->name; ?></a></span></div>
            <div class="controls">
                <a href="<?php echo $item->getLink('edit') ?>" class="sh" title="<?php echo t('Edit'); ?>"><i class="icon-pencil"></i></a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>