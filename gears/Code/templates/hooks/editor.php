<a data-fancybox-type="iframe" href="<?php echo l('/admin/code/') ?>?splash" class="btn" id="insert-code">
    <?php echo icon('bookmark') . ' ' . t("Вставить код"); ?>
</a>
<script>
    $(document).ready(function(){
        $('#insert-code').fancybox({
            maxWidth	: 1000,
            maxHeight	: 750,
            fitToView	: true,
            width		: '95%',
            height		: '95%',
            autoSize	: true,
            closeClick	: true,
            openEffect	: 'none',
            closeEffect	: 'none'
        });
    });
</script>
<style>
</style>