<ul class="list <?php if ($options->dragndrop): ?>dd-list <?php endif; ?>shd">
    <?php foreach ($items as $item): ?>
        <li id="item-<?php echo $item->id ?>" class="<?php if ($options->dragndrop): ?>dd-item<?php endif; ?>" <?php if ($options->tree): ?>data-level="<?php echo $item->level ?>"<?php endif; ?>>
            <div class="name sh"><span <?php if ($options->tree): ?> class="l<?php echo $item->level ?>"<?php endif; ?>><?php if ($options->dragndrop): ?><i class="icon-move handler"></i><?php endif; ?> <a href="<?php echo $item->getLink() ?>"><?php echo $item->name ?></a></span></div>
            <div class="controls">
                <a href="<?php echo $item->getLink('edit') ?>" class="sh" title="<?php echo t('Edit'); ?>"><i class="icon-pencil"></i></a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>