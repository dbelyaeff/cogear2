<ul class="dd-list shd">
    <?php foreach ($pages as $page): ?>
        <li id="page-<?php echo $page->id ?>" class="dd-item" data-level="<?php echo $page->level?>">
            <div class="dd-name sh"><span class="l<?php echo $page->level?>"><i class="icon-move dd-handler"></i> <a href="<?php echo $page->getLink() ?>"><?php echo $page->name ?></a></span></div>
            <div class="dd-controls">
                <a href="<?php echo $page->getLink('edit') ?>" class="sh" title="<?php echo t('Edit'); ?>"><i class="icon-pencil"></i></a>
                <span class="sh"><?php echo icon('eye-' . ($page->published ? 'open' : 'close')); ?></span>
            </div>
        </li>
    <?php endforeach; ?>
</ul>