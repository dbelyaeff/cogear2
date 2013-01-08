<div class="attached-file well shd">
    <?php $file = new File($file->path,$file);?>
    <?php echo $file->render();?>
    <a href="<?php echo $file->getLink('delete')?>" class="sh" title="<?php echo t('Удалить')?>"><i class="icon-remove"></i></a>
    <a href="#" class="sh" title="<?php echo t('Вставить')?>"><i class="icon-upload"></i></a>
</div>