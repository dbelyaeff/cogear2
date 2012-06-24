<div class="tags-cloud">
    <?php
    $max = max($counter);
    ?>
    <?php foreach ($tags as $tag): ?>
        <?php
            $level = '1.'.$max/$counter[$tag->id];
        ?>
        <a style="font-size: <?php echo $level;?>em" href="<?php echo l('/tags/' . $tag->name); ?>"><?php echo $tag->name ?></a>
    <?php endforeach; ?>
</div>