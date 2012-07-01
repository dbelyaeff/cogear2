<div id="admin-menu" class="subnav">
    <ul class="nav nav-pills">
        <?php foreach ($menu as $element): ?>
            <li class="<?php echo $element->class.' '.($element->active ? 'active' : ''); ?>">
                <?php if ($element->link): ?>
                    <a href="<?php echo $element->link ?>"><?php echo $element->label; ?></a>
                <?php else: ?>
                    <?php echo $element->label ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>