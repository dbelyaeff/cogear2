<div class="dd shd" data-saveuri="<?php echo $options->saveUri ?>">
    <ul class="dd-list">
        <?php $last_item = NULL; ?>
        <?php foreach ($items as $item): ?>
            <?php
            if ($last_item) {
                if (strlen($item->thread) > strlen($last_item->thread) && strpos($item->thread, $last_item->thread) === 0) {
                    echo '<ul class="dd-list">';
                } else if (strlen($item->thread) < strlen($last_item->thread)) {
                    echo str_repeat('</ul>',$last_item->level - $item->level);
                } else {
                    echo '</li>';
                }
            } else {
                echo '</li>';
            }
            ?>
            <li class="dd-item" data-id="<?php echo $item->id ?>"><?php echo $item->render('admin.list') ?>

                <?php
                $last_item = $item;
            endforeach;
            ?>
    </ul>
</div>
<p class="t_c">
<button id="dd-save"  class="btn btn-primary btn-large"><?php echo t('Сохранить') ?></button>
</p>
