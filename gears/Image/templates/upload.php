<form method="POST" action="/image/upload" class="horizontal" enctype="multipart/form-data" id="form-image-upload" style="display: none;">
    <div class="page-header"><h4><?php echo t('Загрузка изображений') ?></h4></div>
    <input name="images[]" type="file" id="form-image-upload-image-element" data-url="<?php echo l('/image/upload') ?>" multiple style="display: none;">
    <div class="well t_c" onclick="$('#form-image-upload-image-element').click()" style="cursor:pointer;"><p><?php echo t('Кликните или перетащите файлы сюда при помощи мыши') ?></b></div>
    <script src="<?php echo cogear()->gears->File->folder?>/js/vendor/jquery.ui.widget.js"></script>
    <script src="<?php echo cogear()->gears->File->folder?>/js/jquery.iframe-transport.js"></script>
    <script src="<?php echo cogear()->gears->File->folder?>/js/jquery.fileupload.js"></script>
</form>