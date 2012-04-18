<div class="modal hide fade" id="<?php echo $id?>"<?php if(!$settings->show):?> style="display:none"<?php endif;?>>

    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <?php if ($header): ?>   <h3><?php echo $header ?></h3><?php endif; ?>
    </div>

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