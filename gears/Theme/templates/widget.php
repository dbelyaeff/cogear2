<div class="widget shd">
    <?php if (access('Theme.widgets')): ?>
        <a class="sh edit" href="<?php echo l('/admin/theme/widgets/' . $widget->id . '/options') ?>"><i class="icon icon-pencil"></i></a>
    <?php endif; ?>
    <?php echo $content ?>
</div>