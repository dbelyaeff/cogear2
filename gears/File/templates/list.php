<div id="files">
    <div id="files-wrapper">
        <ul class="thumbnails shd">
            <?php foreach ($files as $file): ?>
                <li class="thumbnail" id="file-<?php echo $file->id ?>"><div class="sh"><?php echo $file->render() ?>
                        <i data-id="<?php echo $file->id ?>" class="icon icon-remove"></i>
                    </div></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<script>
    $('#files .icon-remove').on('click',function(){
        $id = $(this).attr('data-id');
        $(this).removeClass("icon-remove").addClass("icon-time");
        $.getJSON('<?php echo l('/files/ajax/delete/') ?>'+$id,function(data){
            if(data.success){
                $('#file-'+$id).remove();
            }
            else {
                $(this).addClass("icon-remove").removeClass("icon-time");
            }
        })
    })
</script>
<style>
    #files a{
        display: block;
    }
    #files img{
        height: 93px;
    }
    #files .thumbnail{
        width: 130px;
        text-align: center;
        padding: 2px;
    }
    #files .thumbnail > div{
        background: #F1F1F1;
        position: relative;
    }
    #files .thumbnail .icon-remove, #files .thumbnail .icon-time{
        position: absolute;
        top: 2px;
        right: 2px;
    }
</style>