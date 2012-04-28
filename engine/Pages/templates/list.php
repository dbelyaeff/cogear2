<ul class="dd-list">
    <?php foreach ($pages as $page): ?>
        <li id="page-<?php echo $page->id ?>" class="alert l<?php echo $page->level?>">
            <div class="dd-name"><a href="<?php echo $page->getEditLink() ?>"><?php echo $page->name ?></a></div>
            <div class="dd-controls">
                <a href="<?php echo $page->getEditLink() ?>" class="btn btn-primary btn-mini"><?php echo t('Edit'); ?></a>
                <?php echo icon('eye-' . ($page->published ? 'open' : 'close')); ?>
            </div>1
        </li>
    <?php endforeach; ?>
</ul>