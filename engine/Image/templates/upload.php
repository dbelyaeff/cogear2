<form method="POST" action="/image/upload" class="horizontal" enctype="multipart/form-data" id="form-image-upload" style="display: none;">
    <div class="page-header"><h1><?php echo t('Upload image', 'Image') ?></h1></div>
    <div class="control-group t_c" id="form-image-upload-image">
        <label class="control-label" for="image"><?php echo t('Choose from disk', 'Image') ?></label><div class="controls"><input name="images[]" type="file" id="form-image-upload-image-element" data-url="<?php echo l('/image/upload') ?>" multiple>
        </div>
    </div>
    <div class="well t_c"><?php echo t('OR'); ?><p><b><?php echo t('Drag and drop your images here') ?></b></p></div>
</form>