<div class="image-preview" id="image-preview-<?=$attributes['name']?>">
<?if($value):?>
    <?=HTML::img($value,'',$image)?>
<?else:?>
    <span></span>
<?endif;?>
</div>
<?=HTML::input($attributes)?>

<script type="text/javascript">
    $(document).ready(function(){
        $('input[name=<?=$attributes['name']?>]').ajaxFileUpload({
            target: '#image-preview-<?=$attributes['name']?> > <?=$value?'img':'span'?>'
        });
    });
</script>