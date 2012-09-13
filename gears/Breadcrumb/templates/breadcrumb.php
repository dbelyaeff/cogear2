<ul class="breadcrumb">
    <?php
    $i = 0;
    foreach ($menu as $item):
        ?>
        <?php if ($i < ($menu->count() - 1)): ?>
            <li>
                <a href="<?php echo $item->link ?>"><?php echo $item->label ?></a> <span class="divider">/</span>
            </li>
        <?php else: ?>
            <li class="active">
                <?php echo $item->label ?></a>
            </li>
        <?php endif; ?>
        <?php
        $i++;
    endforeach;
    ?>
</ul>