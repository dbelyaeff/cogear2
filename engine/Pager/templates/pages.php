<div class="pagination">
    <ul>
        <?php if ($first) { ?><li><a href="<?php echo $base_uri . $first ?>/">&laquo;</a></li><?php } ?>
        <?php if ($prev) { ?><li><a class="prev" href="<?php echo $base_uri . $prev ?>/">&larr;</a></li><?php } ?>
        <?php
        if ($order == Pager::FORWARD) {
            for ($i = 1; $i <= $pages_num; $i++):
                ?>
                <li class="<?php if ($i == $current) { ?>active<?php } ?>">
                    <a href="<?php echo $base_uri . $i ?>/"><?php echo $i ?></a>
                </li>
                <?php
            endfor;
        }
        if ($order == Pager::REVERSE) {
            for ($i = $pages_num; $i > 0; $i--):
                ?>
                <li class="<?php if ($i == $current) { ?>active<?php } ?>">
                    <a href="<?php echo $base_uri . $i ?>/"><?php echo $i ?></a>
                </li>
                <?php
            endfor;
        }
        ?>

        <?php if ($next) { ?><li><a class="next" href="<?php echo $base_uri . $next ?>/">&rarr;</a></li><?php } ?>
        <?php if ($last) { ?><li><a href="<?php echo $base_uri . $last ?>/">&raquo;</a></li><?php } ?>
    </ul>
</div>