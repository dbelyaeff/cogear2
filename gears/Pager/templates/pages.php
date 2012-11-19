<div class="pagination">
    <ul>
        <?php if ($first) { ?><li><a href="<?php echo $base ?>">&laquo;</a></li><?php } ?>
        <?php if ($prev) { ?><li><a class="prev" href="<?php echo $base . $prefix . $prev ?>">&larr;</a></li><?php } ?>
        <?php
        if ($order == Pager::FORWARD) {
            $a = 5;
            $b = $a + 1;
            for ($i = 1; $i <= $pages_num; $i++):
                if($pages_num > 12){
                    if($i == $current - $b OR $i == $current + $b){
                        ?>
                         <li><a>…</a></li>
                        <?
                    }
                    if($current < 9 && $i > 12){
                        continue;
                    }
                    if($i > $current + $a OR $i < $current - $a){
                        continue;
                    }
                }
                ?>
                <li class="<?php if ($i == $current) { ?>active<?php } ?>">
                    <a href="<?php echo $base . ( $i != $first ? $prefix . $i : '') ?>"><?php echo $i ?></a>
                </li>
                <?php
            endfor;
        }
        if ($order == Pager::REVERSE) {
            for ($i = $pages_num; $i > 0; $i--):
                ?>
                <li class="<?php if ($i == $current) { ?>active<?php } ?>">
                    <a href="<?php echo $base . $prefix . $i ?>/"><?php echo $i ?></a>
                </li>
                <?php
            endfor;
        }
        ?>

        <?php if ($next) { ?><li><a class="next" href="<?php echo $base . $prefix . $next ?>">&rarr;</a></li><?php } ?>
        <?php if ($last) { ?><li><a href="<?php echo $base . $prefix . $last ?>">&raquo;</a></li><?php } ?>
    </ul>
</div>