<div class="well t_c" id="drop-area">
    <b><?php echo t('Перетаскивайте файлы сюда') ?></b><br/>
    <?php echo t('или') ?><br/>
    <button class="btn" id="upload-button"><i class="icon icon-upload"></i> <?php echo t('Выберите файлы') ?></button>
</div>
<style>
    #drop-area{
        border: 2px dashed #CCC;
        line-height: 30px;
        -webkit-transition: 0.5s ease;
        -moz-transition: 0.5s ease;
        -ms-transition: 0.5s ease;
        -o-transition: 0.5s ease;
        transition: 0.5s ease;
    }
    #drop-area b{
        font-size: 1.2em;
    }
    #drop-area .icon {
        margin: 0 0 3px 0;
    }
    #drop-area.dragover{
        background: #FEFEFE;
    }
</style>
<script>
    $('#upload-button').uploader({
       url: '<?php echo l('/files/upload')?>',
       drop_element: 'drop-area',
//       filters: <?php //echo json_encode($filters)?>,
       onComplete: function(data){
       }
    });
</script>