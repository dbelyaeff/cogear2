<div class="navbar <?php echo $menu->class?>">
    <div class="navbar-inner">
        <div class="container">
            <?php if ($brand = $menu->filter(array('place' => 'brand'))): ?>
                <?php foreach ($brand as $item): ?>
                    <a class="brand" <?php if($item->title){ echo ' title="'.$item->title.'"';}?> href="<?php echo $item->link; ?>"><?php echo $item->label ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($left = $menu->filter(array('place' => 'left'))): ?>
                <ul class="nav">
                    <?php foreach ($left as $item): ?>
                    <li class="<?php if($item->active) echo 'active'?>"><a <?php if($item->title){ echo ' title="'.$item->title.'"';}?>  href="<?php echo $item->link; ?>"><?php echo $item->label ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($right = $menu->filter(array('place' => 'right'))): ?>
                <ul class="nav pull-right">
                    <?php foreach ($right as $item): ?>
                    <li class="<?php if($item->active) echo 'active'?>"><a <?php if($item->title){ echo ' title="'.$item->title.'"';}?>  href="<?php echo $item->link; ?>"><?php echo $item->label ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>