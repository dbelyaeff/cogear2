<a class="btn" id="upload-button">
    <?php echo icon('upload') . ' ' . t("Загрузить изображение"); ?>
</a>
<script>
    new Uploader({
        url: '<?php echo l('/files/upload/editor/image') ?>',
        drop_element: $('[name=body]').attr('id'),
        browse_button: 'upload-button',
        uploadProgress: function(file){
            cogear.ajax.loader.type('blue-dots').after($('#upload-button')).show();
        },
        onComplete: function(data){
            if(data.code){
                $el = $('[name=body]');
                window.insertWysiwyg("\n<p align=\"center\">" + data.code + "</p>\n");
            }
            cogear.ajax.loader.hide();
        }
    });
</script>
<style>
    textarea.dragover{
        border: 1px dashed #CCC;
        background: #F1F1F1;
    }
    .ajax-loader.blue-dots{
        margin: 9px 3px;
        vertical-align: middle;
    }
</style>