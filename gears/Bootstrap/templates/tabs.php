<ul class="nav nav-tabs <?php echo $menu->class?>" id="menu-<?php echo $menu->name?>">
    <?php $last_item = NULL; ?>
    <?php foreach ($menu as $item): ?>
        <?php
        if ($last_item) {
            if (substr_count($item->order,'.') > substr_count($last_item->order,'.') && strpos($item->order, $last_item->order) === 0) {
                echo '<div class="dropdown"><ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">';
            } else if (substr_count($item->order,'.') < substr_count($last_item->order,'.')) {
                echo str_repeat('</ul></div>', $last_item->level - $item->level);
            } else {
                echo '</li>';
            }
        } else {
            echo '</li>';
        }
        ?>
        <li class="<?php if($item->active){ echo 'active';}?> <?php echo $item->class?>"> <a class="<?php echo $item->link_class?>" href="<?php echo $item->link?>"><?php echo $item->label?></a>
            <?php
            $last_item = $item;
        endforeach;
        ?>
</ul>
