<div class="modal <?php if(!$settings->show):?>hide fade<?php endif;?>" id="<?php echo $id?>"<?php if($source):?> data-source="<?php echo $source?>"<?php endif;?>>

    <?php if ($header): ?> <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo $header ?></h3>
    </div>
    <?php else:?>
    <a class="close close-standalone" data-dismiss="modal">×</a>
    <?php endif; ?>

    <div class="modal-body">
        <p><?php echo $body ?></p>
    </div>
    <?php if ($actions->count()): ?>
        <div class="modal-footer">
            <?php foreach ($actions as $button): ?>
                <a class="<?php echo $button->class ?>" href="<?php echo $button->link ?>"><?php echo $button->label ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>